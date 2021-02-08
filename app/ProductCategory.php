<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;


class ProductCategory extends Model
{
    use SoftDeletes, NodeTrait;

    protected $fillable = ['name', 'company_id', 'parent_id'];

    protected $appends = ['full_name'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    /*
    public function parent(): HasOne
    {
        return $this->hasOne(ProductCategory::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }
*/
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent_id) {
            return $this->parent->full_name . ' > ' . $this->name;
        } else {
            return $this->name;
        }
    }
}
