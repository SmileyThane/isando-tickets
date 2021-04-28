<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\RoleRepository;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepo = $roleRepository;
    }

    public function roles()
    {
        return self::showResponse(true, Role::where(['company_id' => Auth::user()->employee->company_id])->get());
    }

    public function permissions()
    {
        return self::showResponse(true, Permission::all());
    }

    public function getRolesWithPermissions()
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PERMISSION_READ_ACCESS)) {
            return self::showResponse(true, $this->roleRepo->getRolesWithPermissions());
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
