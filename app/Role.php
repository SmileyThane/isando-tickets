<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
