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

     public function add(Request $request)
    {
        $phone = $this->socialRepo->create($request['entity_id'], $request['entity_type'], $request['social_link'], $request['social_type']);
        return self::showResponse(true, $phone);
    }

    public function edit(Request $request, $id)
    {
        $phone = $this->socialRepo->update($id, $request['social_type'], $request['social_link']);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->socialRepo->delete($id));
    }

    public function addType(Request $request)
    {
        $type = $this->socialRepo->createType($request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->socialRepo->updateType($id, $request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function deleteType($id)
    {
        return self::showResponse($this->socialRepo->deleteType($id));
    }

    public function getTypes()
    {
        return self::showResponse(true, $this->socialRepo->getTypesInCompanyContext());
    }
}
