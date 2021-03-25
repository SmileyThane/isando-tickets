<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class KbArticle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'category_id', 'company_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KbCategory::class, 'category_id', 'id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
