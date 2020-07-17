<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    //

    public function socialable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(SocialType::class, 'id', 'social_type');
    }
}
