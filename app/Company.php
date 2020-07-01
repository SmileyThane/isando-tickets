<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    public function getRegistrationDateAttribute()
    {
        return $this->attributes['registration_date'] ? Carbon::parse($this->attributes['registration_date'])->format('Y-m-d') : null;
    }

    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyUser::class, 'company_id', 'id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyProduct::class, 'company_id', 'id');
    }

    public function clients()
    {
        return $this->morphMany(Client::class, 'supplier');
    }

    public function teams()
    {
        return $this->morphMany(Team::class, 'team_owner');
    }

}
