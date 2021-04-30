<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\ClientGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientGroupController extends Controller
{
    private $clientGroupRepo;

    public function __construct(ClientGroupRepository $clientGroupRepo)
    {
        $this->clientGroupRepo = $clientGroupRepo;
    }

    public function create(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->clientGroupRepo->create($request->name, $request->company_id));
        }

        return self::showResponse(false);
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->clientGroupRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function addClient(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            $result = $this->clientGroupRepo->addClient($request->client_ids, $request->client_group_id);
            return self::showResponse($result);
        }

        return self::showResponse(false);
    }

    public function removeClient($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->clientGroupRepo->removeClient($id));
        }

        return self::showResponse(false);
    }

    public function addCompanyUser(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            $result = $this->clientGroupRepo->addCompanyUser($request->company_user_ids, $request->client_group_id);
            return self::showResponse($result);
        }

        return self::showResponse(false);
    }

    public function removeCompanyUser($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->clientGroupRepo->removeCompanyUser($id));
        }

        return self::showResponse(false);
    }
}
