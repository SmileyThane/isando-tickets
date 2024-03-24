<?php


namespace App\Http\Controllers\API;

use App\ClientFilterGroup;
use App\Http\Controllers\Controller;
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
