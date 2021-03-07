<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Social extends Model
{
    use SoftDeletes;

    protected $fillable = ['entity_id', 'entity_type', 'social_link', 'social_type'];

    public function socialable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(SocialType::class, 'id', 'social_type');
    }
}
