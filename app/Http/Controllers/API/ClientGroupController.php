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
            $query = ClientFilterGroup::query()
                ->where('company_id', '=', $employee->company_id);

            if ($request->view_as_tree == 1) {
                $query->where('parent_id', '=', null);
                $query->with('children');
            }

            $result = $query->get();
        }
        return self::showResponse(true, $result);
    }

    public function create(Request $request): JsonResponse
    {
        $employee = $companyId = Auth::user()->employee;
        $group = new ClientFilterGroup();
        $group->parent_id = $request->parent_id;
        $group->name = $request->name;
        $group->company_id = $employee->company_id;
        $group->save();

        return self::showResponse(true, $group);
    }

    public function delete($id)
    {
        ClientFilterGroup::query()->where('id', '=', $id)->delete();

        return self::showResponse(true);
    }

}
