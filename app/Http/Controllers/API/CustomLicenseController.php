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
}
