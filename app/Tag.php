<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    protected $fillable = ['id', 'name', 'color'];

    public static function boot(): void
    {

        parent::boot();

        static::deleted(static function ($tag) {
            DB::table('taggables')
                ->where('tag_id', '=', $tag->id)
                ->delete();
        });
    }

    public static function randomHexColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
