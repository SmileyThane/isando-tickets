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
        $tags = Tag::with('translates')
            ->where('lang', '=', 'default')
            ->whereNull('tag_id');
        if ($request->has('search')) {
            return $tags
                ->where('name', 'LIKE', '%' . $request->get('search') . '%')
                ->get();
        }
        return $tags->get();
    }

    public function find(Tag $tag)
    {
        return $tag;
    }

    public function create(Request $request)
    {
        $tag = Tag::where('name', '=', $request->name)->first();
        if (!$tag) {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->color = $request->color;
            $tag->save();

            $tagTranslation = new Tag([
                'name' => $request->name,
                'color' => $request->color,
                'lang' => \Auth::user()->language->locale,
                'tag_id' => $tag->id
            ]);
            $tagTranslation->save();
        }
        return $tag;
    }

    public function update(Request $request, Tag $tag)
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

    public function delete(Tag $tag)
    {
        try {
            $tag->delete();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function createOrUpdateTranslation($request, $tag) {
        $request->validate([
            'lang' => 'required|string',
            'name' => 'required|string'
        ]);
        $tagTranslation = Tag::where('tag_id', '=', $tag->id)
            ->where('lang', '=', $request->lang)
            ->first();
        if (!$tagTranslation) {
            $tagTranslation = new Tag();
        }
        $tagTranslation->tag_id = $tag->id;
        $tagTranslation->lang = $request->lang;
        $tagTranslation->color = $tag->color;
        $tagTranslation->name = $request->name;
        $tagTranslation->save();
        return $tagTranslation;
    }
}
