<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeLimitationGroup extends Model
{
    protected $fillable = ['company_user_id', 'client_group_id'];

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function clientGroup(): HasOne
    {
        return $this->hasOne(LimitationGroup::class, 'id', 'client_group_id');
    }
}
