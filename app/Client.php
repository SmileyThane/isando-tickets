<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public function clientable()
    {
        return $this->morphTo();
    }

    public function clients()
    {
        return $this->morphMany( self::class, 'supplier');
    }

    public function allClients()
    {
        return $this->clients()->with('allClients');
    }

    public function teams()
    {
        return $this->morphMany( Team::class, 'team_owner');
    }

    public function employees()
    {
        return $this->hasMany( ClientCompanyUser::class, 'client_id', 'id');
    }
}
