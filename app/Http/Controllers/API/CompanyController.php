<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyUserRepository;
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
        return self::showResponse(true,
            [
                'id' => Auth::user()->employee->companyData->id,
                'first_alias' => Auth::user()->employee->companyData->first_alias,
                'second_alias' => Auth::user()->employee->companyData->second_alias
            ]);
    }

    public function mainCompanyLogo(): JsonResponse
    {
        return self::showResponse(true, Auth::user()->employee->companyData->logo_url);
    }

    public function find(Request $request, $id = null): JsonResponse
    {
        $hasAccess = Auth::user()->employee->hasPermissionId([
            Permission::COMPANY_READ_ACCESS,
            Permission::CLIENT_READ_ACCESS
        ]);
        if ($hasAccess) {
            return self::showResponse(true, $this->companyRepo->find($request, $id));
        }

        return self::showResponse(false);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function invite(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return $this->companyUserRepo->invite($request);
        }

        return null;
    }

    public function removeEmployee($id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_DELETE_ACCESS)) {
            return self::showResponse($this->companyUserRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function attachProduct(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::COMPANY_WRITE_ACCESS)) {
            return self::showResponse($this->companyRepo->attachProduct($request));
        }

        return self::showResponse(false);
    }

    public function getIndividuals(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::INDIVIDUAL_READ_ACCESS)) {
            return self::showResponse(true, $this->companyUserRepo->all($request));
        }
        return self::showResponse(false);
    }

    public function attachProductCategory(Request $request): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse($this->companyRepo->attachProductCategory(
                $request->name, $request->company_id, $request->parent_id
            ));
        }

        return self::showResponse(false);
    }

    public function detachProductCategory($id): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse($this->companyRepo->detachProductCategory($id));
        }
        return self::showResponse(false);
    }

    public function getProductCategoriesTree($id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->getProductCategoriesTree($id));
        }

        return self::showResponse(false);
    }

    public function getProductCategoriesFlat(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_READ_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->getProductCategoriesFlat($id));
        }

        return self::showResponse(false);
    }

    public function getSettings(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_READ_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->getSettings($id));
        }
        return self::showResponse(false);
    }

    public function updateSettings(Request $request, $id = null): JsonResponse
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_WRITE_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->updateSettings($request, $id));
        }
        return self::showResponse(false);
    }

    public function updateLogo(Request $request, $id = null)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::SETTINGS_WRITE_ACCESS)) {
            return self::showResponse(true, $this->companyRepo->updateLogo($request, $id));
        }
        return self::showResponse(false);
    }
}
