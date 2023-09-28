<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
    public const SUPERADMIN = 1;
    public const CO_ADMIN = 2;
    public const ADMIN = 3;
    public const MANAGER = 4;
    public const USER = 5;
    public const COMPANY_CLIENT = 6;
    public const IS_CLIENTABLE = 101;

    //staff
    public const HIGH_PRIVIGIES = [Role::CO_ADMIN, Role::ADMIN, Role::MANAGER];

    // this is a virtual role
    protected $table = 'roles';

    protected $fillable = ['id', 'company_id', 'name'];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function permissionIds()
    {
        return $this->permissions ? $this->permissions->pluck('id')->toArray() : [];
    }

    /**
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::snake($value),
        );
    }
}
