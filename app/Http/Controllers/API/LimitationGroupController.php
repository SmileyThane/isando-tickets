<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\LimitationGroup;
use App\LimitationType;
use App\Permission;
use App\Repositories\LimitationGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimitationGroupController extends Controller
{
    private $limitationGroupRepo;

    public function __construct(LimitationGroupRepository $limitationGroupRepo)
    {
        $this->limitationGroupRepo = $limitationGroupRepo;
    }

    public function create(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse(
                $this->limitationGroupRepo->create($request->name, $request->company_id, $request->limitation_type_id, $request->auto_assign)
            );
        }

        return self::showResponse(false);
    }

    public function types()
    {
        return self::showResponse(true, LimitationType::all());
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->limitationGroupRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function addLimitationModel(Request $request)
    {
        $limitationGroup = LimitationGroup::find($request->limitation_group_id);
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS) && $limitationGroup) {
            $limitationGroup = LimitationGroup::find($request->limitation_group_id);
            $result = $this->limitationGroupRepo->addLimitationModel(
                $request->model_ids,
                $request->limitation_group_id,
                $limitationGroup->type->model
            );
            return self::showResponse($result);
        }

        return self::showResponse(false);
    }

    public function removeLimitationModel($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->limitationGroupRepo->removeModel($id));
        }

        return self::showResponse(false);
    }

    public function addCompanyUser(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            $result = $this->limitationGroupRepo
                ->addCompanyUser($request->company_user_ids, $request->limitation_group_id);
            return self::showResponse($result);
        }

        return self::showResponse(false);
    }

    public function removeCompanyUser($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->limitationGroupRepo->removeCompanyUser($id));
        }

        return self::showResponse(false);
    }
}
