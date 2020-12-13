<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\ClientRepository;
use App\Repository\CompanyUserRepository;
use App\Repository\UserRepository;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    protected $userRepo;
    protected $clientRepo;
    protected $companyUserRepo;

    public function __construct(UserRepository $userRepository, ClientRepository $clientRepository, CompanyUserRepository $companyUserRepository)
    {
        $this->userRepo = $userRepository;
        $this->clientRepo = $clientRepository;
        $this->companyUserRepo = $companyUserRepository;
    }

    public function all(Request $request)
    {
        $all = $this->clientRepo->all($request);
        return self::showResponse(true, $all);
    }

    public function clients(Request $request)
    {
        $clients = $this->clientRepo->clients($request);
        return self::showResponse(true, $clients);
    }

    public function get(Request $request)
    {
        $clients = $this->clientRepo->clients($request);
        return self::showResponse(true, $clients);
    }

    public function suppliers(Request $request)
    {
        $suppliers = $this->clientRepo->suppliers($request);
        return self::showResponse(true, $suppliers);
    }

    public function find($id)
    {
        $client = $this->clientRepo->find($id);
        return self::showResponse(true, $client);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->clientRepo->validate($request);
        if ($result === true) {
            $result = $this->clientRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->clientRepo->validate($request, false);
        if ($result === true) {
            $result = $this->clientRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete(Request $request, $id)
    {
        $result = $this->clientRepo->delete($id);
        return self::showResponse($result);
    }

    public function attach(Request $request)
    {
        $request['role_id'] = Role::COMPANY_CLIENT;
        $request['company_id'] = Auth::user()->employee->company_id;
        $request['is_clientable'] = true;
        if (!$request->company_user_id) {
            $inviteResponse = $this->companyUserRepo->invite($request);
            $inviteResult = $inviteResponse->getOriginalContent();
            if ($inviteResult['success'] === true) {
                $request['company_user_id'] = $inviteResult['data']['id'];
            } else {
                return $inviteResponse;
            }
        }
        $result = $this->clientRepo->attach($request);
        return self::showResponse($result);
    }

    public function detach(Request $request, $id)
    {
        $result = $this->clientRepo->detach($id);
        return self::showResponse($result);
    }

    public function changeIsActive(Request $request)
    {
        $result = $this->clientRepo->changeIsActive($request);
        return self::showResponse($result);
    }

    public function recipientsTree(Request $request)
    {
        $result = $this->clientRepo->getClientsAsRecipientsTree($request);
        return self::showResponse(true, $result);
    }
}
