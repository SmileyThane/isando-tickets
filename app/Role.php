<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPERADMIN = 1;
    public const LICENSE_OWNER = 2;
    public const ADMIN = 3;
    public const MANAGER = 4;
    public const USER = 5;
    public const COMPANY_CLIENT = 6;
    public const IS_CLIENTABLE = 101;

    //staff
    public const HIGH_PRIVIGIES = [Role::LICENSE_OWNER, Role::ADMIN, Role::MANAGER];

    // this is a virtual role
    protected $table = 'roles';

    public function permissions(): HasMany
    {
        return $this->hasMany(RoleHasPermission::class, 'role_id', 'id');
    }

    public function permissionIds()
    {
        $permissionIds = RoleHasPermission::where('role_id', $this->id)->get();
        return $permissionIds ? $permissionIds->pluck('permission_id')->toArray() : [];
    }
}
