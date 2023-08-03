<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class CompanyUser extends Model
{
    use SoftDeletes, HasRoles;

    protected $table = 'company_users';

    protected $appends = ['role_names', 'color'];

    protected $with = ['roles'];

    protected $fillable = [
        'user_id',
        'company_id',
        'description',
        'is_clientable',
    ];

    public function getRoleNamesAttribute()
    {
        $result = null;
        $rolesCount = count($this->roles);
        $translationsArray = Language::query()->find(Auth::user()->language_id)->lang_map;
        foreach ($this->roles as $key => $role) {
            $roleName = $role->name;
            if (property_exists($translationsArray->roles, $roleName)) {
                $result .= $translationsArray->roles->$roleName;
            } else {
                $result .= $roleName;
            }
            $result .= $key !== $rolesCount - 1 ? ', ' : '';
        }
        return $result ?? $translationsArray->roles->contact;
    }

    public function roleIds()
    {
        return $this->roles
            ->pluck('id')
            ->toArray();
    }

    public function getPermissionIds()
    {
        $roleIds = $this->roleIds();
        if ($roleIds) {
            return RoleHasPermission::query()->whereIn('role_id', $roleIds)->get()->pluck('permission_id')->toArray();
        }
        return [];
    }

    public function hasRoleId($id)
    {
        $roleIds = $this->roleIds();
        if ($roleIds) {
            if (is_int($id)) {
                return in_array($id, $roleIds, true);
            } else {
                foreach ($id as $item) {
                    return in_array($item, $roleIds, true);
                }
            }
        }

        return false;
    }

    public function hasPermissionId($id)
    {
        $id = is_int($id) ? [$id] : $id;
        $roleIds = $this->roleIds();
        if ($roleIds) {
            return RoleHasPermission::query()->whereIn('role_id', $roleIds)->whereIn('permission_id', $id)->exists();
        }
        return false;
    }

    public function assignedToTeams(): HasMany
    {
        return $this->hasMany(TeamCompanyUser::class, 'company_user_id', 'id');
    }

    public function assignedToProducts(): HasMany
    {
        return $this->hasMany(ProductCompanyUser::class, 'company_user_id', 'id');
    }

    public function assignedToClients(): HasMany
    {
        return $this->hasMany(ClientCompanyUser::class, 'company_user_id', 'id')->withTrashed();
    }

    public function userData(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function companyData(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function getColorAttribute()
    {
        return $this->userData ? $this->userData->color : '';
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'model');
    }
}
