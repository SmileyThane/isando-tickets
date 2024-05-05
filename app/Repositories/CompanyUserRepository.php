<?php


namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\Email;
use App\Http\Controllers\Controller;
use App\LimitationGroupHasModel;
use App\Notifications\RegularInviteEmail;
use App\Permission;
use App\Phone;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class CompanyUserRepository
{

    protected $userRepo;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
    }

    public function all(Request $request)
    {
//                        DB::enableQueryLog();
        if ($request->sort_by && strpos($request->sort_by, '.')) {
            $request->sort_by = substr($request->sort_by, strrpos($request->sort_by, '.') + 1);
        }

        $orderBy = $request->sort_by ?? 'name';
        $request->sort_by = null;

        $employee = Auth::user()->employee;
        $clientIds = [];
        if ($employee->hasPermissionId([Permission::CLIENT_GROUPS_DEPENDENCY])) {
            $assignedClients = [];
            $clientGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Client::class);
            if ($clientGroups) {
                $assignedClients = LimitationGroupHasModel::query()
                    ->whereIn('limitation_group_id', $clientGroups->pluck('id')->toArray())
                    ->get();
            }
            $clientIdsQuery = Client::query()
                ->whereIn('id', $assignedClients->pluck('model_id')->toArray());
            if ($request->with_trashed) {
                $clientIdsQuery->withTrashed();
            }
            $clientIds = $clientIdsQuery->get()
                ->pluck('id')
                ->toArray();
            $freeCompanyUsers = [];
        } elseif (!$employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientIdsQuery = Client::where([
                'supplier_type' => Company::class,
                'supplier_id' => $employee->company_id
            ]);
            if ($request->with_trashed) {
                $clientIdsQuery->withTrashed();
            }
            $clientIds = $clientIdsQuery->get()->pluck('id')->toArray();
            $freeCompanyUsersQuery = CompanyUser::where([
                ['company_id', $employee->company_id],
                ['is_clientable', true]
            ]);
            if ($request->with_trashed) {
                $freeCompanyUsersQuery->withTrashed();
            }

            $freeCompanyUsers = $freeCompanyUsersQuery->get()->pluck('id')->toArray();
        }

        $clientCompanyUsers = ClientCompanyUser::whereIn('client_id', $clientIds);
        if ($request->with_trashed) {
            $clientCompanyUsers->withTrashed();
        }
        $companyUsers = CompanyUser::whereIn(
            'id',
            array_merge(
                $clientCompanyUsers->get()->pluck('company_user_id')->toArray(),
                $freeCompanyUsers
            )
        )
            ->has('userData')
            ->with(['userData' => function ($query) use ($request) {
                if ($request->with_trashed) {
                    $query->withTrashed();
                }
            },
                'assignedToClients.clients',
                'userData.emails.type',
                'userData.phones.type',
                'userData.companies',
                'userData.settings',
            ]);

        if ($request->with_trashed) {
            $companyUsers->withTrashed();
        }

        if ($request->search) {
            $request['page'] = 1;
            $companyUsers = $companyUsers->whereHas(
                'userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('number', 'like', '%' . $request->search . '%')
                        ->orWhere('surname', 'like', '%' . $request->search . '%');
                    if ($request->with_trashed) {
                        $query->withTrashed();
                    }
                }
            );
        }
        $companyUsers = $companyUsers->get();

        $orderFunc = function ($item, $key) use ($orderBy) {
            switch ($orderBy) {
                case 'id':
                    return $item->userData->id;
                case 'number':
                    $item->userData->number;
                case 'name':
                    return mb_strtolower($item->userData->name);
                case 'surname':
                    return mb_strtolower($item->userData->surname);
                case 'email':
                case 'emails':
                    $email = $item->userData->contact_email;
                    return $email ? mb_strtolower($email->email) : '';
                case 'is_active':
                    return $item->userData->is_active ? 1 : 0;
                case 'status':
                    return $item->userData->status ? 1 : 0;
                case 'client':
                case 'clients':
                    if ($item->assignedToClients) {
                        return mb_strtolower($item->assignedToClients[0]->clients->name);
                    } else {
                        return '';
                    }
            }
        };

        if ($request->sort_val === 'false') {
            $companyUsers = $companyUsers->sortBy($orderFunc);
        } else {
            $companyUsers = $companyUsers->sortByDesc($orderFunc);
        }

//                dd(DB::getQueryLog());
        return $companyUsers->paginate($request->per_page ?? $companyUsers->count());
    }

    public function find($id)
    {
        return CompanyUser::withTrashed()->find($id);
    }

    public function delete($id)
    {
        $result = false;
        $companyUser = CompanyUser::find($id);
        if ($companyUser) {
            Email::query()
                ->where('entity_id', $companyUser->user_id)
                ->where('entity_type', User::class)
                ->delete();
            User::where('id', $companyUser->user_id)->delete();
            ClientCompanyUser::where('company_user_id', $companyUser->id)->delete();
            $companyUser->delete();
            $result = true;
        }
        return $result;
    }

    public function invite(Request $request)
    {
        $isClientable = $request['is_clientable'] ?? false;
        $isNew = $isRestored = false;
        $request['password'] = Controller::getRandomString();
        $request['password_confirmation'] = $request['password'];
        $email = Email::withTrashed()->where('entity_type', User::class)->where('email', $request['email'])->first();
        if ($email) {
            if ($email->trashed()) {
                switch ($request['action']) {
                    case User::ACTION_RESTORE:
                        $user = User::withTrashed()->find($email->entity_id);
                        $this->userRepo->restoreDeleted($user->id);
                        $isRestored = true;
                        break;
                    case User::ACTION_CREATE:
                        $email->editExistingEmail();
                        $isValid = $this->userRepo->validate($request, $new = true);
                        if ($isValid !== true) {
                            return Controller::showResponse(false, $isValid);
                        }
                        $user = $this->userRepo->create($request);
                        $isNew = true;
                        break;
                    default:
                        return ['email_trashed' => true];
                }
            } else {
                $user = User::find($email->entity_id);
            }
        } else {
            $isValid = $this->userRepo->validate($request, $new = true);
            if ($isValid !== true) {
                return Controller::showResponse(false, $isValid);
            }
            $user = $this->userRepo->create($request);
            $isNew = true;
        }
        $request['user_id'] = $user->id;
        $isValid = $this->validate($request, $isRestored);
        if ($isValid === true) {
            $companySettings = Settings::firstOrCreate([
                'entity_id' => $request['company_id'],
                'entity_type' => Company::class
            ], [
                'data' => []
            ]);
            if (!empty($companySettings->data['timezone'])) {
                $user->timezone_id = $companySettings->data['timezone'];
                $user->save();
            }
            $companyUser = CompanyUser::firstOrCreate([
                'user_id' => $request['user_id'],
                'company_id' => $request['company_id'],
            ],
                [
                    'is_clientable' => $isClientable,
                    'description' => $request['description'],
                ]);
            if ($user->is_active) {
                $this->roleRepo->attach($companyUser->id, CompanyUser::class, $request['role_id']);
                if ($isNew === true) {
                    try {
                        @$user->notify(
                            new RegularInviteEmail(
                                $companyUser->companyData->title,
                                $companyUser->companyData->name,
                                $user->full_name,
                                $request['email'],
                                $request['password'],
                                $user->language->short_code
                            )
                        );
                    } catch (Throwable $throwable) {
                        Log::error($throwable);
                        //hack for broken notification system
                    }
                }
            }

            if ($request['phones']) {
                foreach ($request['phones'] as $phone) {
                    Phone::firstOrCreate([
                        'entity_id' => $user->id,
                        'entity_type' => User::class,
                        'phone' => $phone['phone'],
                        'phone_type' => $phone['phone_type']
                    ]);

                }
            }

            return $companyUser;
        }
        return $isValid;
    }

    public function validate($request, bool $isRestored = true)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'user_id' => [
                'required',
                $isRestored ? '' : Rule::unique('company_users')
                    ->where(function ($query) use ($request) {
                        return $query->where('company_id', $request['company_id'])
                            ->where('user_id', $request['user_id']);
                    }),
            ],
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function create($companyId, $userId, $isClientable, $description = null): CompanyUser
    {
        $companyUser = new CompanyUser();
        $companyUser->user_id = $userId;
        $companyUser->company_id = $companyId;
        $companyUser->is_clientable = $isClientable;
        $companyUser->description = $description;
        $companyUser->save();
        return $companyUser;
    }
}
