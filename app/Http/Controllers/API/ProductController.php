<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepo = $productRepository;
    }

    public function get(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_READ_ACCESS)) {
            return self::showResponse(true, $this->productRepo->all($request));
        }

        return self::showResponse(false);
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_READ_ACCESS)) {
            return self::showResponse(true, $this->productRepo->find($id));
        }

        return self::showResponse(false);
    }

    public function create(Request $request)
    {
        $isValid = $this->productRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS);

        if ($isValid && $hasAccess) {
            return self::showResponse(true, $this->productRepo->create($request));
        }

        return self::showResponse(false);
    }

    public function update(Request $request, $id)
    {
        $isValid = $this->productRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS);

        if ($isValid && $hasAccess) {
            return self::showResponse(true, $this->productRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_DELETE_ACCESS)) {
            return self::showResponse($this->productRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function attachEmployee(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse($this->productRepo->attachEmployee($request));
        }

        return self::showResponse(false);
    }

    public function detachEmployee($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse($this->productRepo->detachEmployee($id));
        }

        return self::showResponse(false);
    }

    public function attachClient(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse(true, $this->productRepo->attachClient($request));
        }

        return self::showResponse(false);
    }

    public function detachClient($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::PRODUCT_WRITE_ACCESS)) {
            return self::showResponse(true, $this->productRepo->detachClient($id));
        }

        return self::showResponse(false);
    }
}
