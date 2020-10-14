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
        $phone = $this->socialRepo->update($id, $request['phone'], $request['phone_type']);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->socialRepo->delete($id));
    }

    public function addType(Request $request)
    {
        $type = $this->socialRepo->createType($request->name, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->socialRepo->updateType($id, $request->name, $request->icon);
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

    public function getAllTypes()
    {
        return self::showResponse(true, $this->socialRepo->getAllTypes());
    }

    public function getCompanyTypes()
    {
        return self::showResponse(true, $this->socialRepo->getCompanyTypeIds());
    }

    public function addCompanyType(Request $request)
    {
        $country = $this->socialRepo->createCompanyType($request->social_type_id, $request->company_id);
        return self::showResponse(true, $country);
    }
    public function deleteCompanyType(Request $request, $id)
    {
        return self::showResponse($this->socialRepo->deleteCompanyType($id, $request->company_id));
    }
}
