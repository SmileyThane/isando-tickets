<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Role;
use App\Settings;
use App\User;
use App\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyUserRepository
{

    protected $userRepo;
    protected $roleRepo;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
    }

    public function all(Request $request)
    {
        if ($request->sort_by && strpos($request->sort_by, '.')) {
            $request->sort_by = substr($request->sort_by, strrpos($request->sort_by, '.') + 1);
        }

        $orderBy = $request->sort_by ?? 'name';
        $request->sort_by = null;

        $employee = Auth::user()->employee;
        $clientIds = $employee->hasRole(Role::COMPANY_CLIENT) ? null :
            Client::where([
                'supplier_type' => Company::class,
                'supplier_id' => $employee->company_id
            ])->get()->pluck('id')->toArray();
        $clientCompanyUsers = ClientCompanyUser::whereIn('client_id', $clientIds)->get()->pluck('company_user_id')->toArray();
        $freeCompanyUsers = CompanyUser::where([['company_id', $employee->company_id], ['is_clientable', true]])->get()->pluck('id')->toArray();

        $companyUsers = CompanyUser::whereIn('id', array_merge($clientCompanyUsers, $freeCompanyUsers));
        if ($request->search) {
            $request['page'] = 1;
            $companyUsers->whereHas(
                'userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('surname', 'like', $request->search . '%');
                }
            );
        }

        $companyUsers = $companyUsers->with(['assignedToClients.clients', 'userData.emails.type'])->get();

        $orderFunc = function ($item, $key) use ($orderBy) {
            switch ($orderBy) {
                case 'id':
                    return $item->userData->id;
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

        return $companyUsers->paginate($request->per_page ?? $companyUsers->count());
    }

    public function find($id)
    {
        return CompanyUser::find($id);
    }

    public function delete($id)
    {
        $result = false;
        $companyUser = CompanyUser::find($id);
        if ($companyUser && !$companyUser->hasRole(Role::LICENSE_OWNER)) {
            User::where('id', $companyUser->user_id)->delete();
            ClientCompanyUser::where('company_user_id', $companyUser->id)->delete();
            $companyUser->delete();
            $result = true;
        }
        return $result;
    }

    public function invite(Request $request)
    {
        $isClientable =  $request['is_clientable'] ?? false;
        $isNew = false;
        $request['password'] = Controller::getRandomString();
        $email = Email::where('entity_type', User::class)->where('email', $request['email'])->first();
        if ($email) {
            $user = User::find($email->entity_id);
        } else {
            $isValid = $this->userRepo->validate($request, $new = true);
            if ($isValid !== true) {
                return Controller::showResponse(false, $isValid);
            }
            $user = $this->userRepo->create($request);
            $isNew = true;
        }
        $request['user_id'] = $user->id;
        $isValid = $this->validate($request);
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
            $companyUser = $this->create($request['company_id'], $request['user_id'], $isClientable);
            if ($user->is_active) {
                $this->roleRepo->attach($companyUser->id, CompanyUser::class, $request['role_id']);
                if ($isNew === true) {
                    try {
                        @$user->notify(
                            new RegularInviteEmail($companyUser->companyData->title, $companyUser->companyData->name, $user->full_name, $request['role_id'], $request['email'], $request['password'], $user->language->short_code)
                        );
                    } catch (\Throwable $throwable) {
                        Log::error($throwable);
                        //hack for broken notification system
                    }
                }
            }
            return Controller::showResponse(true, $companyUser);
        }
        return Controller::showResponse(false, $isValid);
    }

    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'user_id' => [
                'required',
                Rule::unique('company_users')->where(function ($query) use ($request) {
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
