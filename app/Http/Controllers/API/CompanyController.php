<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\CompanyRepository;
use App\Repository\CompanyUserRepository;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyRepo;
    protected $companyUserRepo;

    public function __construct(CompanyRepository $companyRepository, CompanyUserRepository $companyUserRepository)
    {
        $this->companyRepo = $companyRepository;
        $this->companyUserRepo = $companyUserRepository;
    }

    public function find($id = null)
    {
        $company = $this->companyRepo->find($id);
        return self::showResponse(true, $company);
    }

    public function update(Request $request, $id)
    {
        $company = $this->companyRepo->update($request, $id);
        return self::showResponse(true, $company);
    }

    public function invite(Request $request)
    {
        return $this->companyUserRepo->invite($request);
    }

    public function attachProduct(Request $request)
    {
        $result = $this->companyRepo->attachProduct($request);
        return self::showResponse($result);
    }

}
