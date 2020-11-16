<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;


    public function teams()
    {
        return $this->morphMany(self::class, 'team_owner');
    }

    public function employees()
    {
        return $this->hasMany(TeamCompanyUser::class, 'team_id', 'id')->has('employee.userData');
    }
}
