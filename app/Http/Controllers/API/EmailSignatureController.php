<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\EmailRepository;
use Illuminate\Http\Request;

class EmailSignatureController extends Controller
{
    protected $emailSignatureRepo;

    public function __construct(EmailSignatureRepository $emailsignatureRepository)
    {
        $this->emailSignatureRepo = $emailSignatureRepository;
    }

    public function add(Request $request)
    {
        $signature = $this->emailSignatureRepo->create($request['entity_id'], $request['entity_type'], $request['signature']);
        return self::showResponse(true, $signature);
    }

    public function edit(Request $request, $id)
    {
        $signature = $this->emailSignatureRepo->update($id, $request['signature'],);
        return self::showResponse(true, $signature);
    }

    public function delete($id)
    {
        return self::showResponse($this->emailSignatureRepo->delete($id));
    }

    public function get()
    {
        $signatures = $this->emailSignatureRepo->get($request['entity_id'], $request['entity_type']);
        return self::showResponse(true, $signatures);
    }
}
