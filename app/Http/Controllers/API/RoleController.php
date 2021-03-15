<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\RoleRepository;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepo = $roleRepository;
    }

    public function roles()
    {
        return self::showResponse(true, Role::all());
    }

    public function permissions()
    {
        return self::showResponse(true, Permission::all());
    }

    public function getRolesWithPermissions()
    {
        return self::showResponse(true, $this->roleRepo->getRolesWithPermissions());
    }

    public function mergeRolesWithPermissions(Request $request)
    {
        return self::showResponse(true, $this->roleRepo->mergeRolesWithPermissions($request->data));
    }

}
