<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class CompanyUser extends Model
{
    use SoftDeletes, HasRoles;

    protected $table = 'company_users';

    public function assignedToTeams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamCompanyUser::class, 'company_user_id', 'id');
    }

    public function assignedToProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductCompanyUser::class, 'company_user_id', 'id');
    }

    public function assignedToClients(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClientCompanyUser::class, 'company_user_id', 'id');
    }

    public function userData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function companyData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

}
