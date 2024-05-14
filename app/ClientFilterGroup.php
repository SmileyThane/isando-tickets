<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientFilterGroup extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'number', 'description'];

    protected $appends = ['children'];


    public function getChildrenAttribute()
    {
        return $this->childrenData()->get()->mergeRecursive($this->clients);
    }

    public function childrenData(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parentData(): HasOne
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

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_filter_group_has_clients','id','client_id');
    }
}
