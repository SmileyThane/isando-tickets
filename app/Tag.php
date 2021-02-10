<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    protected $fillable = ['id', 'name', 'color'];

    public static function boot() {

        parent::boot();

        static::deleted(function($tag) {
            DB::table('taggables')
                ->where('tag_id', '=', $tag->id)
                ->delete();
        });

    }
}
