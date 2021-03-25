<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Social extends Model
{
    use SoftDeletes;

    protected $fillable = ['entity_id', 'entity_type', 'social_link', 'social_type'];

    public function socialable(): MorphTo
    {
        return $this->morphTo();
    }

    public function type(): HasOne
    {
        return $this->hasOne(SocialType::class, 'id', 'social_type');
    }
}
