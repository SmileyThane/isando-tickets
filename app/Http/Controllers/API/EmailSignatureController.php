<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\EmailSignatureRepository;
use Illuminate\Http\Request;

class EmailSignatureController extends Controller
{
    protected $emailSignatureRepo;

    public function __construct(EmailSignatureRepository $emailsignatureRepository)
    {
        $this->emailSignatureRepo = $emailsignatureRepository;
    }

    public function add(Request $request)
    {
        $signature = $this->emailSignatureRepo->create($request['entity_id'], $request['entity_type'], $request['name'], $request['signature']);
        return self::showResponse(true, $signature);
    }

    public function update(Request $request, $id)
    {
        $signature = $this->emailSignatureRepo->update($id, $request['name'], $request['signature']);
        return self::showResponse(true, $signature);
    }

    public function delete($id)
    {
        return self::showResponse($this->emailSignatureRepo->delete($id));
    }

    public function get(Request $request)
    {
        $signatures = $this->emailSignatureRepo->get($request['entity_id'], $request['entity_type']);
        return self::showResponse(true, $signatures);
    }
}
