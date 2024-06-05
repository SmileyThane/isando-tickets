<?php


namespace App\Http\Controllers\API;

use App\ClientCompanyUser;
use App\CompanyUser;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\ClientRepository;
use App\Repositories\CompanyUserRepository;
use App\Repositories\LimitationGroupRepository;
use App\Repositories\UserRepository;
use App\Role;
use App\RoleHasPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function all(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->all($request));
        }

        return self::showResponse(false);
    }

    public function clients(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->clients($request));
        }

        return self::showResponse(false);
    }

    public function relatedClients(Request $request, int $id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->relatedClients($request, $id));
        }

        return self::showResponse(false);
    }

    public function suppliers(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->suppliers());
        }

        return self::showResponse(false);
    }

    public function find($id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->find($id));
        }

        return self::showResponse(false);
    }

    public function create(Request $request): JsonResponse
    {
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS);
        $isValid = $this->clientRepo->validate($request);
        if ($isValid === true && $hasAccess) {
            $client = $this->clientRepo->create($request);
            (new LimitationGroupRepository())->limitationAutoAssignProcess($client);
            return self::showResponse(true, $client);
        }

        return self::showResponse(false, $isValid);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS);
        $isValid = $this->clientRepo->validate($request, false, $id);
        if ($isValid === true && $hasAccess) {
            return self::showResponse(true, $this->clientRepo->update($request, $id));
        }

        return self::showResponse(false, $isValid);
    }

    public function delete(Request $request, $id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_DELETE_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function attach(Request $request): JsonResponse
    {
        $result = false;
        $clientRole = null;
        $companyUser = null;

        $validator = Validator::make($request->all(), ['client_id' => 'required']);
        if ($validator->fails()) {
            return self::showResponse(false, $validator->errors());
        }

        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            $roles = Role::query()->where('company_id', Auth::user()->employee->company_id)->get();
            if ($roles) {
                $clientRole = RoleHasPermission::query()
                    ->whereIn('role_id', $roles->pluck('id')->toArray())
                    ->where('permission_id', Permission::EMPLOYEE_CLIENT_ACCESS)
                    ->first();
            }
            $request['role_id'] = $clientRole ? $clientRole->role_id : Role::COMPANY_CLIENT;
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

            $companyUser = $existingClient ?
                $this->clientRepo->updateDescription($request) :
                $this->clientRepo->attach($request);
            $result = !is_null($companyUser);
        }

        return self::showResponse($result, $companyUser);
    }

    public function detach($id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse($this->clientRepo->detach($id));
        }

        return self::showResponse(false);
    }

    public function changeIsActive(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse($this->clientRepo->changeIsActive($request));
        }

        return self::showResponse(false);
    }

    public function recipientsTree(): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_READ_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->getClientsAsRecipientsTree());
        }

        return self::showResponse(false);
    }

    public function updateLogo(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::CLIENT_WRITE_ACCESS)) {
            return self::showResponse(true, $this->clientRepo->updateLogo($request, $id));
        }

        return self::showResponse(false);
    }

    public function updateNotes(Request $request, $id)
    {
        $this->clientRepo->updateNotes($id, $request->notes);

        return self::showResponse(true);
    }
}
