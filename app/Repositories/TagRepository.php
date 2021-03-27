<?php


namespace App\Repositories;

use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagRepository
{

    protected $rules = [
        'name' => 'string',
        'color' => 'string'
    ];

    public function validate($request, $new = true)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        if ($request->has('search')) {
            return Tag::where('name', 'LIKE', '%' . $request->get('search') . '%')
                ->get();
        }
        return Tag::all();
    }

    public function find(Tag $tag): Tag
    {
        return $tag;
    }

    public function create(Request $request): Tag
    {
        $tag = Tag::where('name', '=', $request->name)->first();
        if (!$tag) {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->color = $request->color;
            $tag->save();
        }
        return $tag;
    }

    public function update(Request $request, Tag $tag): Tag
    {
        if ($request->has('name')) {
            $tag->name = $request->name;
        }
        if ($request->has('color')) {
            $tag->color = $request->color;
        }
        $tag->save();
        return $tag;
    }

    public function delete(Tag $tag): ?bool
    {
        try {
            $tag->delete();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
