<?php


namespace App\Repository;

use App\Client;
use App\Company;
use App\Product;
use App\Tag;
use App\Team;
use App\TeamCompanyUser;
use App\Tracking;
use App\TrackingProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagRepository
{

    protected $rules = [
        'name' => 'string'
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

    }

    public function find($id)
    {

    }

    public function create(Request $request)
    {
        $tag = Tag::where('name', '=', $request->name)->first();
        if (!$tag) {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->save();
        }
        return $tag;
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->name = $request->name;
        $tag->save();
        return $tag;
    }

    public function delete(Tracking $tracking)
    {

    }
}
