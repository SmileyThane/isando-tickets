<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeClientGroup extends Model
{
    protected $fillable = ['company_user_id', 'client_group_id'];
}
