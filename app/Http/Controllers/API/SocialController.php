<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\SocialRepository;
use App\SocialType;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    protected $socialRepo;

    public function __construct(SocialRepository $socialRepository)
    {
        $this->socialRepo = $socialRepository;
    }

    public function getTypes()
    {
        return self::showResponse(true, SocialType::all());
    }


    public function add(Request $request)
    {
        $phone = $this->socialRepo->create($request['entity_id'], $request['entity_type'], $request['social_link'], $request['social_type']);
        return self::showResponse(true, $phone);
    }

    public function edit(Request $request, $id)
    {
        $phone = $this->socialRepo->update($id, $request['phone'], $request['phone_type']);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->socialRepo->delete($id));
    }
}
