<?php

namespace App\Http\Controllers\API;

use App\Repository\TagRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    protected $tagRepo = null;

    public function __construct(TagRepository $tagRepository) {
        $this->tagRepo = $tagRepository;
    }

    public function create(Request $request) {
        $success = false;
        $result = $this->tagRepo->validate($request, true);
        if ($result === true) {
            $result = $this->tagRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function get(Request $request) {
        $result = $this->tagRepo->all($request);
        return self::showResponse((bool)$result, $result);
    }
}
