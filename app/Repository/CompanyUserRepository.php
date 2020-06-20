<?php


namespace App\Repository;


use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
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
        if ($companyUser) {
            $companyUser->delete();
            $result = true;
        }
        return $result;
    }

    public function invite(Request $request)
    {
        $isNew = false;
        $request['password'] = Controller::getRandomString();
        $user = User::where('email', $request['email'])->first();
        if (!$user) {
            $user = $this->userRepo->create($request);
            $isNew = true;
        }
        $request['user_id'] = $user->id;
        $isValid = $this->validate($request);
        if ($isValid === true) {
            $companyUser = $this->create($request['company_id'], $request['user_id']);
            $this->roleRepo->attach($companyUser->id, CompanyUser::class, $request['role_id']);
            return Controller::showResponse(true, $companyUser);
        }
        $isNew === true ? $user->notify(new RegularInviteEmail($request['name'], $request['role_id'], $request['email'], $request['password'])) : null;
        return Controller::showResponse(false, $isValid);
    }


}
