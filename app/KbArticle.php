<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;



class KbArticle extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'company_id', 'is_internal', 'featured_color', 'keywords', 'keywords_de'];

    protected $appends = ['featured_image'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(KbCategory::class, 'kb_article_categories', 'article_id', 'category_id');
    }

    public function attachments($lang = null): MorphMany
    {
        if ($lang) {
            return $this->morphMany(File::class, 'model')->where('service_info->lang', $lang);
        } else {
            return $this->morphMany(File::class, 'model');
        }
    }

    public function tags(): MorphToMany {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function previous(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_relations', 'next_article_id', 'article_id');
    }

    public function next(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_relations', 'article_id', 'next_article_id');
    }

    public function delete()
    {
        $this->next()->delete();

        foreach ($this->attachments as $attachment) {
            $attachment->delete();
        }

        return parent::delete();
    }

    public function getFeaturedImageAttribute()
    {
        return $this->attachments()->where('service_info->type', 'kb_featured')->first();
    }

    public function getFeaturedImagesAttribute()
    {
        return $this->attachments()->where('service_info->type', 'kb_featured')->orderBy('service_info->order')->get();
    }
}
