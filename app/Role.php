<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    const LICENSE_OWNER = 2;
    const COMPANY_CLIENT = 6;


}
