<?php


namespace App\Http\Controllers\API;

use App\Client;
use App\Company;
use App\Http\Controllers\Controller;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    protected $userRepo;
    protected $clientRepo;

    public function __construct(UserRepository $userRepository, ClientRepository $clientRepository)
    {
        $this->userRepo = $userRepository;
        $this->clientRepo = $clientRepository;
    }

    public function get(Request $request)
    {
        $clients = $this->clientRepo->all($request);
        return self::showResponse(true, $clients);
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
        $result = $this->clientRepo->attach($request);
        return self::showResponse($result);
    }
}
