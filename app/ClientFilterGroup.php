<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientFilterGroup extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name'];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function getNameAttribute()
    {
        $parent = $this->parent;
        if ($parent) {
            return $parent->name . ': ' . $this->attributes['name'];
        }

        return $this->attributes['name'];
    }
}
