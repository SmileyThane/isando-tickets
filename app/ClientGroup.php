<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    protected $fillable = ['name', 'company_id'];

    public function employees()
    {
        return $this->hasMany(EmployeeClientGroup::class, 'client_group_id', 'id');
    }

    public function clients()
    {
        return $this->hasMany(ClientGroupHasClient::class, 'client_group_id', 'id');
    }
}
