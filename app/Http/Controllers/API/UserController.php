<?php


namespace App\Http\Controllers\API;

use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\IxarmaRepository;
use App\Role;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepo;
    protected $roleRepo;
    protected $ixarmaRepo;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, IxarmaRepository $ixarmaRepository)
    {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
        $this->ixarmaRepo = $ixarmaRepository;
    }

    public function find($id = null)
    {
        $with = $id ? [
            'employee.companyData',
            'employee.assignedToClients.clients'
        ]
            : [];
        $id = $id ?? Auth::id();
        $user = $this->userRepo->find($id, $with);
        return self::showResponse(true, $user);
    }

    public function update(Request $request)
    {
        $id = $request->id ?? Auth::id();
        $success = false;
        $result = null;
        $request['email'] = '[no_email]';
        $isValid = $this->userRepo->validate($request, true);
        if ($isValid === true) {
            $result = $this->userRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result ?? $isValid);
    }

    public function authorizedRoleIds()
    {
        $companyUser = Auth::user()->employee;
        $roles = $companyUser->is_clientable ? [Role::IS_CLIENTABLE] : [];
        return self::showResponse(true, array_merge($roles, $companyUser->roles->pluck('id')->toArray()));
    }

    public function authorizedPermissionIds()
    {
        $companyUser = Auth::user()->employee;
        return self::showResponse(true, $companyUser->getPermissionIds());
    }

    public function updateRoles(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            $request->model_type = CompanyUser::class;
            return self::showResponse($this->roleRepo->updateRoles($request));
        }

        return self::showResponse(false);
    }

    public function changeIsActive(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_WRITE_ACCESS)) {
            return self::showResponse($this->userRepo->changeIsActive($request));
        }

        return self::showResponse(false);
    }


    public function sendInvite(Request $request)
    {
        $success = false;
        $user = User::find($request->user_id);
        if ($user->is_active) {
            $success = $this->userRepo->sendInvite($user);
        }
        return self::showResponse($success);
    }

    public function getSettings(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_READ_ACCESS)) {
            return self::showResponse(true, $this->userRepo->getSettings($id));
        }

        return self::showResponse(false);
    }

    public function updateSettings(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_WRITE_ACCESS)) {
            return self::showResponse(true, $this->userRepo->updateSettings($request, $id));
        }

        return self::showResponse(false);
    }

    public function setNotifications(Request $request): JsonResponse
    {
        return self::showResponse(true, $this->userRepo->setNotificationStatuses($request->user_id, $request->notification_statuses));
    }

    public function updateAvatar(Request $request)
    {
        return self::showResponse(true, $this->userRepo->updateAvatar($request));
    }

    public function delete(Request $request, $id)
    {
        return self::showResponse(true, $this->userRepo->delete($id));
    }

    public function restoreDeleted(Request $request)
    {
        return self::showResponse(true, $this->userRepo->restoreDeleted($request->id));
    }

    public function updateIxarmaLink(Request $request, $id)
    {
        $data = $request->toArray();
        return self::showResponse($this->ixarmaRepo->login($request->login, $request->password, $request->id));
    }
}
