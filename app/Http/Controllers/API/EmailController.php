<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\EmailRepository;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected $emailRepo;

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepo = $emailRepository;
    }

    public function add(Request $request)
    {
        $email = $this->emailRepo->create($request['entity_id'], $request['entity_type'], $request['email'], $request['email_type']);
        return self::showResponse(true, $email);
    }

    public function edit(Request $request, $id)
    {
        $email = $this->emailRepo->update($id, $request['email_type'], $request['email'],);
        return self::showResponse(true, $email);
    }

    public function delete($id)
    {
        return self::showResponse($this->emailRepo->delete($id));
    }

    public function getTypes()
    {
        return self::showResponse(true, $this->emailRepo->getTypesInCompanyContext());
    }

    public function addType(Request $request)
    {
        $type = $this->emailRepo->createType($request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->emailRepo->updateType($id, $request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function deleteType($id)
    {
        return self::showResponse($this->emailRepo->deleteType($id));
    }
}
