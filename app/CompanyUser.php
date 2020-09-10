<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class CompanyUser extends Model
{
    use SoftDeletes, HasRoles;

    protected $table = 'company_users';

    protected $appends = ['roles', 'role_names'];

    public function getRoleNamesAttribute()
    {
        $result = null;
        $roles = $this->getRolesAttribute();
        $translationsArray = Language::find(Auth::user()->language_id)->lang_map;
        foreach ($roles as $key => $role) {
            $roleName = $role->name;
            $result .= $translationsArray->roles->$roleName;
            $result .= $key !== count($roles) - 1 ? ', ' : '';
        }
        return $result ?? 'contact';
    }

    public function getRolesAttribute()
    {
        $roleIds = ModelHasRole::where(['model_id' => $this->attributes['id'], 'model_type' => self::class])->get()->pluck('role_id')->toArray();
        return Role::whereIn('id', $roleIds)->get();
    }

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
