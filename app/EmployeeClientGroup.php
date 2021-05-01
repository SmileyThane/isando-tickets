<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeClientGroup extends Model
{
    protected $fillable = ['company_user_id', 'client_group_id'];

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function clientGroup(): HasOne
    {
        return $this->hasOne(ClientGroup::class, 'id', 'client_group_id');
    }
}
