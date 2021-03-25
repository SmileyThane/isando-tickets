<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'short_code'
    ];

    public function getLangMapAttribute()
    {
        return json_decode($this->attributes['lang_map'], false);
    }
}
