<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    protected $fillable = ['role_id', 'permission_id'];

    public $timestamps = false;

}
