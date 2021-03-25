<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class KbCategory extends Model
{
    use SoftDeletes;
    use NodeTrait;

    protected $fillable = ['name', 'name_de', 'description', 'description_de', 'icon', 'company_id', 'parent_id'];

    protected $appends = ['articles_count', 'categories_count'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(KbArticle::class, 'category_id', 'id');
    }

    public function getArticlesCountAttribute(): int
    {
        return $this->hasMany(KbArticle::class, 'category_id', 'id')->count();
    }

    public function getCategoriesCountAttribute(): int
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id')->count();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent_id) {
            return $this->parent->full_name . ' > ' . $this->name;
        }

        return $this->name;
    }

    public function getFullNameDeAttribute(): string
    {
        if ($this->parent_id) {
            return $this->parent->full_name_de . ' > ' . $this->name_de;
        }

        return $this->name_de;
    }
}
