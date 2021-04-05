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

    protected $fillable = ['name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'company_id', 'prev_id', 'is_internal', 'featured_color'];

    protected $appends = ['featured_image'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(KbCategory::class, 'kb_article_categories', 'article_id', 'category_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function tags(): MorphToMany {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function previous() {
        return KbArticle::where('id', $this->prev_id)->with('tags', 'categories')->first();
    }

    public function next() {
        return KbArticle::where('prev_id', $this->id)->with('tags', 'categories')->first();
    }

    public function delete()
    {
        $next = $this->next();
        if ($next) {
            $next->delete();
        }

        foreach ($this->attachments as $attachment) {
            $attachment->delete();
        }

        return parent::delete();
    }

    public function getFeaturedImageAttribute()
    {
        return $this->attachments()->where('service_info', 'featured')->first();
    }
}
