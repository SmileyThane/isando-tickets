<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
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

    public function all(Request $request)
    {
        $employee = Auth::user()->employee;
        $clientIds = $employee->hasRole(Role::COMPANY_CLIENT) ? null :
            Client::where([
                'supplier_type' => Company::class,
                'supplier_id' => $employee->company_id
            ])->get()->pluck('id')->toArray();
        $clientCompanyUsers = ClientCompanyUser::whereIn('client_id', $clientIds);
        return $clientCompanyUsers
            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
            ->with('employee.userData', 'clients')
            ->paginate($request->per_page ?? $clientCompanyUsers->count());
    }

    public function find($id)
    {
        return CompanyUser::find($id);
    }

    public function create($companyId, $userId)
    {
        $companyUser = new CompanyUser();
        $companyUser->user_id = $userId;
        $companyUser->company_id = $companyId;
        $companyUser->save();
        return $companyUser;
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
            $user = $this->userRepo->create($request);
            $isNew = true;
        }
        $request['user_id'] = $user->id;
        $isValid = $this->validate($request);
        if ($isValid === true) {
            $companyUser = $this->create($request['company_id'], $request['user_id']);
            if ($user->is_active) {
                $this->roleRepo->attach($companyUser->id, CompanyUser::class, $request['role_id']);
                $isNew === true ? $user->notify(new RegularInviteEmail($request['name'], $request['role_id'], $request['email'], $request['password'])) : null;
            }
            return Controller::showResponse(true, $companyUser);
        }
        return Controller::showResponse(false, $isValid);
    }


}
