<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;


class KbCategory extends Model
{
    use SoftDeletes, NodeTrait;

    protected $fillable = ['name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'company_id', 'parent_id'];

    protected $appends = ['articles_count', 'categories_count'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function articles(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_categories', 'category_id', 'article_id');
    }

    public function getArticlesCountAttribute(): int
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_categories', 'category_id', 'article_id')->count();
    }

    public function getCategoriesCountAttribute(): int
    {
        return $this->hasMany(KbCategory::class, 'parent_id', 'id')->count();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent_id) {
            return $this->parent->full_name . ' > ' . $this->name;
        } else {
            return $this->name;
        }
    }

    public function getFullNameDeAttribute(): string
    {
        if ($this->parent_id) {
            return $this->parent->full_name_de . ' > ' . $this->name_de;
        } else {
            return $this->name_de;
        }
    }
}
