<?php


namespace App\Http\Controllers\API;

use App\ClientCompanyUser;
use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\ClientRepository;
use App\Repositories\CompanyUserRepository;
use App\Repositories\UserRepository;
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
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->all($request));
        }

        return self::showResponse(false);
    }

    public function clients(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->clients($request));
        }

        return self::showResponse(false);
    }

    public function relatedClients(Request $request, int $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->relatedClients($request, $id));
        }

        return self::showResponse(false);
    }

//  deprecated method
//    public function get(Request $request)
//    {
//        $clients = $this->clientRepo->clients($request);
//        return self::showResponse(true, $clients);
//    }

    public function suppliers(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->suppliers($request));
        }

        return self::showResponse(false);
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->find($id));
        }

        return self::showResponse(false);
    }

    public function create(Request $request)
    {
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS);
        $isValid = $this->clientRepo->validate($request);

        if ($isValid && $hasAccess) {
            return self::showResponse(true, $this->clientRepo->create($request));
        }

        return self::showResponse(true);
    }

    public function update(Request $request, $id)
    {
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS);
        $isValid = $this->clientRepo->validate($request, false);

        if ($isValid && $hasAccess) {
            return self::showResponse(true, $this->clientRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function delete(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_DELETE_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function attach(Request $request)
    {
        $result = false;
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            $request['role_id'] = Role::COMPANY_CLIENT;
            $request['company_id'] = Auth::user()->employee->company_id;
            $request['is_clientable'] = true;

            if (!$request->company_user_id) {
                $invite = $this->companyUserRepo->invite($request);
                if ($invite instanceof CompanyUser) {
                    $request['company_user_id'] = $invite->id;
                } else {
                    return self::showResponse(false, $invite);
                }
            }

            $existingClient = ClientCompanyUser::where([
                'client_id' => $request->client_id,
                'company_user_id' => $request->company_user_id
            ])->exists();

            $result = $existingClient ?
                $this->clientRepo->updateDescription($request) :
                $this->clientRepo->attach($request);
        }

        return self::showResponse($result);
    }

    public function detach($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse($this->clientRepo->detach($id));
        }

        return self::showResponse(false);
    }

    public function changeIsActive(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse($this->clientRepo->changeIsActive($request));
        }

        return self::showResponse(false);
    }

    public function recipientsTree()
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->getClientsAsRecipientsTree());
        }

        return self::showResponse(false);
    }

    public function updateLogo(Request $request, $id = null)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->updateLogo($request, $id));
        }

        return self::showResponse(false);
    }
}
