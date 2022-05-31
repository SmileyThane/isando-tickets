<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    protected $fillable = ['id', 'name', 'color', 'tag_id', 'lang'];

    public static function boot()
    {

        parent::boot();

        static::deleted(function ($tag) {
            $tagIds = Tag::where('tag_id', '=', $tag->id)->pluck('id')->toArray();
            Tag::whereIn('id', $tagIds)->delete();
            array_push($tagIds, $tag->id);
            DB::table('taggables')
                ->whereIn('tag_id', $tagIds)
                ->delete();
        });
    }

    public static function randomHexColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public function Translates()
    {
        return $this->hasMany(Tag::class, 'tag_id', 'id');
    }
}
