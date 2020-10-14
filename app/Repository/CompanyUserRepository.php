<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanySettings;
use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $employee = Auth::user()->employee;
        $clientIds = $employee->hasRole(Role::COMPANY_CLIENT) ? null :
            Client::where([
                'supplier_type' => Company::class,
                'supplier_id' => $employee->company_id
            ])->get()->pluck('id')->toArray();
        $clientCompanyUsers = ClientCompanyUser::whereIn('client_id', $clientIds);
        if ($request->search !== '') {
            $clientCompanyUsers->whereHas(
                'employee.userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('surname', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                }
            );
        }
        return $clientCompanyUsers
            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
            ->with('employee.userData', 'clients')
            ->paginate($request->per_page ?? $clientCompanyUsers->count());
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
            $companyUser->delete();
            $result = true;
        }
        return $result;
    }

    public function invite(Request $request)
    {
        $isNew = false;
        $request['password'] = Controller::getRandomString();
        $user = User::where(['is_active' => true, 'email' => $request['email']])->first();
        if (!$user) {
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
            $companySettings = CompanySettings::where('company_id', $request['company_id'])->firstOrCreate();
            if (!empty($companySettings->data['timezone'])) {
                $user->timezone_id = $companySettings->data['timezone'];
                $user->save();
            }
            $companyUser = $this->create($request['company_id'], $request['user_id']);
            if ($user->is_active) {
                $this->roleRepo->attach($companyUser->id, CompanyUser::class, $request['role_id']);
                if ($isNew === true) {
                    $user->notify(
                        new RegularInviteEmail($companyUser->companyData->name, $request['name'], $request['role_id'], $request['email'], $request['password'])
                    );
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

    public function create($companyId, $userId): CompanyUser
    {
        $companyUser = new CompanyUser();
        $companyUser->user_id = $userId;
        $companyUser->company_id = $companyId;
        $companyUser->save();
        return $companyUser;
    }


}
