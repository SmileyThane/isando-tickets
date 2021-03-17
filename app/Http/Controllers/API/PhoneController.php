<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\PhoneRepository;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    protected $phoneRepo;

    public function __construct(PhoneRepository $phoneRepository)
    {
        $this->phoneRepo = $phoneRepository;
    }

    public function add(Request $request)
    {
        $phone = $this->phoneRepo->create($request['entity_id'], $request['entity_type'], $request['phone'], $request['phone_type']);
        return self::showResponse(true, $phone);
    }

    public function edit(Request $request, $id)
    {
        $phone = $this->phoneRepo->update($id, $request['phone_type'], $request['phone'],);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->phoneRepo->delete($id));
    }

    public function getTypes()
    {
        return self::showResponse(true, $this->phoneRepo->getTypesInCompanyContext());
    }

    public function addType(Request $request)
    {
        $type = $this->phoneRepo->createType($request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->phoneRepo->updateType($id, $request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function deleteType($id)
    {
        return self::showResponse($this->phoneRepo->deleteType($id));
    }
}
