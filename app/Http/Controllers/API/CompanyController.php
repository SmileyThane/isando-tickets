<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\CompanyRepository;
use App\Repository\CompanyUserRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $companyRepo;
    protected $companyUserRepo;

    public function __construct(
        CompanyRepository $companyRepository,
        CompanyUserRepository $companyUserRepository
    )
    {
        $this->companyRepo = $companyRepository;
        $this->companyUserRepo = $companyUserRepository;
    }

    public function mainCompanyName()
    {
        return self::showResponse(true, Auth::user()->employee->companyData->name);
    }

    public function find(Request $request, $id = null)
    {
        $company = $this->companyRepo->find($request, $id);
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

    public function removeEmployee($id)
    {
        return self::showResponse($this->companyUserRepo->delete($id));

    }

    public function attachProduct(Request $request)
    {
        return self::showResponse($this->companyRepo->attachProduct($request));
    }


    public function getIndividuals(Request $request)
    {
        return self::showResponse(true, $this->companyUserRepo->all($request));
    }

}
