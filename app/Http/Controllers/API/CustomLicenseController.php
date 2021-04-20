<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\CustomLicenseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLicenseController extends Controller
{
    protected $customLicenseRepository;

    public function __construct(CustomLicenseRepository $customLicenseRepository)
    {
        $this->customLicenseRepository = $customLicenseRepository;
    }

    public function index(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_READ_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->index($request));
        }

        return self::showResponse(false);
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_READ_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->find($id));
        }

        return self::showResponse(false);
    }

    public function users($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_READ_ACCESS)) {
            $result = $this->customLicenseRepository->getUsers($id);
            return self::showResponse(true, $result);
        }

        return self::showResponse(false);
    }

    public function manageUser($id, $remoteUserId, $isLicensed)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            $result = $this->customLicenseRepository->manageUser($id, $remoteUserId, $isLicensed);
            return self::showResponse(is_array($result), $result);
        }

        return self::showResponse(false);
    }

    public function history($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_READ_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->itemHistory($id));
        }

        return self::showResponse(false);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function updateLimits(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            $result = $this->customLicenseRepository->updateLimits($request, $id);
            return self::showResponse(is_array($result), $result);
        }

        return self::showResponse(false);
    }

    public function unassignedIxarmaUsersList()
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->unassignedIxarmaUsersList());
        }

        return self::showResponse(false);
    }

    public function assignToIxarmaCompany(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            $result = $this->customLicenseRepository->assignToIxarmaCompany($request);
            return self::showResponse(is_array($result), $result);
        }

        return self::showResponse(false);
    }

    public function unassignFromIxarmaCompany(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            return self::showResponse($this->customLicenseRepository->unassignFromIxarmaCompany($request));
        }

        return self::showResponse(false);
    }

    public function setUserTrial(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::IXARMA_WRITE_ACCESS)) {
            return self::showResponse(true, $this->customLicenseRepository->setUserTrial($request, $id));
        }

        return self::showResponse(false);
    }

}
