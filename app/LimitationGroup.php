<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LimitationGroup extends Model
{
    protected $fillable = ['name', 'company_id', 'limitation_type_id'];

    public function employees(): HasMany
    {
        return $this->hasMany(EmployeeLimitationGroup::class, 'limitation_group_id', 'id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(LimitationType::class, 'id', 'limitation_type_id');
    }

    public function limitationModels(): HasMany
    {
        return $this->hasMany(LimitationGroupHasModel::class, 'limitation_group_id', 'id');
    }
}
