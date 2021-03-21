<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class KbArticle extends Model
{
    use SoftDeletes, NodeTrait;

    protected $fillable = ['name', 'name_de', 'content', 'content_de', 'company_id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
