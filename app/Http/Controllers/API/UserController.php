<?php


namespace App\Http\Controllers\API;

use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Role;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepo;
    protected $roleRepo;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
    }

    public function find($id = null)
    {
        $with = $id ? ['employee.companyData', 'employee.assignedToClients.clients'] : [];
        $id = $id ?? Auth::id();
        $user = $this->userRepo->find($id, $with);
        return self::showResponse(true, $user);
    }

    public function update(Request $request)
    {
        $id = $request->id ?? Auth::id();
        $success = false;
        $result = null;
        $isValid = $this->userRepo->validate($request, true);
        dd($isValid);
        if ($isValid === true) {
            $result = $this->userRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result ?? $isValid);
    }

    public function roles()
    {
        return self::showResponse(true, Role::all());
    }

    public function authorizedRoleIds()
    {
        $companyUser = Auth::user()->employee;
        return self::showResponse(true, $companyUser->roles->pluck('id')->toArray());
    }

    public function updateRoles(Request $request)
    {
        $request->model_type = CompanyUser::class;
        $result = $this->roleRepo->updateRoles($request);
        return self::showResponse($result);
    }

    public function changeIsActive(Request $request)
    {
        return self::showResponse($this->userRepo->changeIsActive($request));
    }


    public function sendInvite(Request $request)
    {
        $success = false;
        $user = User::find($request->user_id);
        if ($user->is_active) {
            $success = $this->userRepo->sendInvite($user, $request->role_id);
        }
        return self::showResponse($success);
    }

    public function getSettings(Request $request, $id = null): JsonResponse
    {
        return self::showResponse(true, $this->userRepo->getSettings($id));
    }

    public function updateSettings(Request $request, $id = null): JsonResponse
    {
        return self::showResponse(true, $this->userRepo->updateSettings($request, $id));
    }
}


// Happy 25th birthday, Hleb!!!
