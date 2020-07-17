<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\PhoneType;
use App\Repository\PhoneRepository;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    protected $phoneRepo;

    public function __construct(PhoneRepository $phoneRepository)
    {
        $this->phoneRepo = $phoneRepository;
    }

    public function getTypes()
    {
        return self::showResponse(true, PhoneType::all());
    }


    public function add(Request $request)
    {
        $phone = $this->phoneRepo->create($request['entity_id'], $request['entity_type'], $request['phone'], $request['phone_type']);
        return self::showResponse(true, $phone);
    }

    public function edit(Request $request, $id)
    {
        $phone = $this->phoneRepo->update($id, $request['phone'], $request['phone_type']);
        return self::showResponse(true, $phone);
    }

    public function delete($id)
    {
        return self::showResponse($this->phoneRepo->delete($id));
    }
}
