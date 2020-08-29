<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    //

    public function getLangMapAttribute()
    {
        return json_decode($this->attributes['lang_map']);
    }
}
