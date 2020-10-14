<?php

namespace App\Http\Controllers\API;

use App\AddressType;
use App\Http\Controllers\Controller;
use App\Repository\AddressRepository;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressRepo;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepo = $addressRepository;
    }

    public function add(Request $request)
    {
        $address = $this->addressRepo->create($request['entity_id'], $request['entity_type'], $request['address'], $request['address_type']);
        return self::showResponse(true, $address);
    }

    public function edit(Request $request, $id)
    {
        $phone = $this->addressRepo->update($id, $request['address'], $request['address_type']);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->addressRepo->delete($id));
    }

    public function addType(Request $request)
    {
        $type = $this->addressRepo->createType($request->name, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->addressRepo->updateType($id, $request->name, $request->icon);
        return self::showResponse(true, $type);
    }

    public function deleteType($id)
    {
        return self::showResponse($this->addressRepo->deleteType($id));
    }

    public function getTypes()
    {
        return self::showResponse(true, $this->addressRepo->getTypesInCompanyContext());
    }

    public function getAllTypes()
    {
        return self::showResponse(true, $this->addressRepo->getAllTypes());
    }

    public function getCompanyTypes()
    {
        return self::showResponse(true, $this->addressRepo->getCompanyTypeIds());
    }

    public function addCompanyType(Request $request)
    {
        $country = $this->addressRepo->createCompanyType($request->address_type_id, $request->company_id);
        return self::showResponse(true, $country);
    }
    public function deleteCompanyType(Request $request, $id)
    {
        return self::showResponse($this->addressRepo->deleteCompanyType($id, $request->company_id));
    }
}
