<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\NodeTrait;


class KbCategory extends Model
{
    use SoftDeletes, NodeTrait;

    protected $fillable = ['name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'company_id', 'parent_id', 'type_id', 'is_internal'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function articles(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_categories', 'category_id', 'article_id');
    }

//    public function getArticlesCountAttribute(): int
//    {
//        return $this->belongsToMany(KbArticle::class, 'kb_article_categories', 'category_id', 'article_id')->count();
//    }

    public function getCategoriesCountAttribute(): int
    {
        return $this->hasMany(KbCategory::class, 'parent_id', 'id')->count();
    }

    public function getFullNameAttribute()
    {
        if ($this->parent_id) {
            return $this->parent->full_name . ' > ' . $this->getTranslatedName();
        } else {
            return $this->getTranslatedName();
        }
    }

    protected function hasSubCategories(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool) $this->children
        );
    }

    private function getTranslatedName()
    {
        if(Auth::user()->language_id === 2) {
            return $this->name_de;
        }

        return $this->name ?? '';
    }
}
