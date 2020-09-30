<?php


namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use App\Repository\CompanyRepository;
use App\Repository\CompanyUserRepository;
use Illuminate\Http\JsonResponse;
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

    public function mainCompanyName(): JsonResponse
    {
        return self::showResponse(true, Auth::user()->employee->companyData->name);
    }

    public function find(Request $request, $id = null): JsonResponse
    {
        $company = $this->companyRepo->find($request, $id);
        return self::showResponse(true, $company);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $company = $this->companyRepo->update($request, $id);
        return self::showResponse(true, $company);
    }

    public function invite(Request $request)
    {
        return $this->companyUserRepo->invite($request);
    }

    public function removeEmployee($id): JsonResponse
    {
        return self::showResponse($this->companyUserRepo->delete($id));

    }

    public function attachProduct(Request $request): JsonResponse
    {
        return self::showResponse($this->companyRepo->attachProduct($request));
    }

    public function getIndividuals(Request $request): JsonResponse
    {
        return self::showResponse(true, $this->companyUserRepo->all($request));
    }

    public function attachProductCategory(Request $request): JsonResponse
    {
        return self::showResponse($this->companyRepo->attachProductCategory($request));
    }

    public function detachProductCategory($id): JsonResponse
    {
        return self::showResponse($this->companyRepo->detachProductCategory($id));
    }

    public function getProductCategoriesTree(Request $request, $id = null): JsonResponse
    {
        return self::showResponse(true, $this->companyRepo->getProductCategoriesTree($id));
    }

    public function getProductCategoriesFlat(Request $request, $id = null): JsonResponse
    {
        return self::showResponse(true, $this->companyRepo->getProductCategoriesFlat($id));
    }

}
