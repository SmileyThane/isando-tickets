<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\CustomLicenseRepository;
use Illuminate\Http\Request;

class CustomLicenseController extends Controller
{
    protected $customLicenseRepository;

    public function __construct(CustomLicenseRepository $customLicenseRepository)
    {
        $this->customLicenseRepository = $customLicenseRepository;
    }

    public function index(Request $request)
    {
        return self::showResponse(true, $this->customLicenseRepository->index($request));
    }

    public function find($id)
    {
        return self::showResponse(true, $this->customLicenseRepository->find($id));
    }

    public function users($id)
    {
        return self::showResponse(true, $this->customLicenseRepository->getUsers($id));
    }

    public function manageUser($id, $remoteUserId, $isLicensed)
    {
        $result = $this->customLicenseRepository->manageUser($id, $remoteUserId, $isLicensed);
        return self::showResponse(is_array($result), $result);
    }

    public function history($id)
    {
        return self::showResponse(true, $this->customLicenseRepository->itemHistory($id));
    }

    public function update(Request $request, $id)
    {
        return self::showResponse(true, $this->customLicenseRepository->update($request, $id));
    }

    public function updateLimits(Request $request, $id)
    {
        $result = $this->customLicenseRepository->updateLimits($request, $id);
        return self::showResponse(is_array($result), $result);
    }

    public function unassignedIxarmaUsersList()
    {
        return self::showResponse(true, $this->customLicenseRepository->unassignedIxarmaUsersList());
    }

    public function assignToIxarmaCompany(Request $request)
    {
        return self::showResponse(true, $this->customLicenseRepository->assignToIxarmaCompany($request));
    }
}
