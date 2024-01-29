<?php


namespace App\Http\Controllers\API;

use App\ClientCompanyUser;
use App\ClientFilterGroup;
use App\ClientFilterGroupHasClients;
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

class ClientGroupController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        $result = [];

        $employee = $companyId = Auth::user()->employee;
        if ($employee) {
            $result = ClientFilterGroup::query()->where('company_id', '=', $employee->company_id)->get();
        }
        return self::showResponse(true, $result);
    }
}
