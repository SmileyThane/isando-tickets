<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\GetRolesRequest;
use App\Permission;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepo = $roleRepository;
    }

    public function roles(GetRolesRequest $request)
    {
        $roles = $this->roleRepo->getAllByCompanyId($request->all(), Auth::user()->employee->company_id);

        return self::showResponse(true, $roles);
    }

    public function permissions()
    {
        return self::showResponse(true, Permission::all());
    }

    public function getRolesWithPermissions(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PERMISSION_READ_ACCESS)) {
            return self::showResponse(true, $this->roleRepo->getRolesWithPermissions($request->all()));
        }
        return self::showResponse(false);

    }

    public function mergeRolesWithPermissions(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PERMISSION_WRITE_ACCESS)) {
            return self::showResponse(true, $this->roleRepo->mergeRolesWithPermissions($request->data));
        }
        return self::showResponse(false);
    }

}
