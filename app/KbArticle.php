<?php

namespace App;

use App\Http\Controllers\API\KbController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class KbArticle extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'company_id', 'is_internal', 'featured_color', 'keywords', 'keywords_de', 'type_id', 'owner_id', 'approved_at', 'importance_id', 'is_draft'];

    protected $appends = ['featured_image', 'importance'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(KbCategory::class, 'kb_article_categories', 'article_id', 'category_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function previous(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_relations', 'next_article_id', 'article_id');
    }

    public function delete()
    {
        $this->next()->delete();

        foreach ($this->attachments as $attachment) {
            $attachment->delete();
        }

        return parent::delete();
    }

    public function next(): belongsToMany
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_relations', 'article_id', 'next_article_id');
    }

    public function getFeaturedImageAttribute()
    {
        return $this->attachments()->where('service_info->type', 'kb_featured')->first();
    }

    public function attachments($lang = null): MorphMany
    {
        if ($lang) {
            return $this->morphMany(File::class, 'model')->where('service_info->lang', $lang);
        } else {
            return $this->morphMany(File::class, 'model');
        }
    }

    public function getFeaturedImagesAttribute()
    {
        return $this->attachments()->where('service_info->type', 'kb_featured')->orderBy('service_info->order')->get();
    }

    public function getOwnerAttribute()
    {
        return CompanyUser::query()->find($this->owner_id);
    }

    public function getImportanceAttribute()
    {

        return $this->importance_id ? KbController::IMPORTANCE[$this->importance_id] : '';
    }

    public function clients(): belongsToMany
    {
        return $this->belongsToMany(Client::class, 'kb_article_clients', 'client_id', 'kb_article_id');
    }

    public function getNameAttribute($value): ?string
    {
        if(Auth::user()->language_id === 2) {
            return $this->name_de;
        }

        return $value;
    }
}
