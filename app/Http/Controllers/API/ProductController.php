<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $userRepo;
    protected $productRepo;

    public function __construct(UserRepository $userRepository, ProductRepository $productRepository)
    {
        $this->userRepo = $userRepository;
        $this->productRepo = $productRepository;
    }

    public function get(Request $request)
    {
        $clients = $this->productRepo->all($request);
        return self::showResponse(true, $clients);
    }

    public function find($id)
    {
        $client = $this->productRepo->find($id);
        return self::showResponse(true, $client);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->productRepo->validate($request);
        if ($result === true) {
            $result = $this->productRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->productRepo->validate($request, false);
        if ($result === true) {
            $result = $this->productRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete(Request $request, $id)
    {
        $result = $this->productRepo->delete($id);
        return self::showResponse($result);
    }

    public function attachEmployee(Request $request)
    {
        $result = $this->productRepo->attachEmployee($request);
        return self::showResponse($result);
    }

    public function detachEmployee(Request $request, $id)
    {
        $result = $this->productRepo->detachEmployee($request, $id);
        return self::showResponse($result);
    }

    public function attachClient(Request $request)
    {
        $result = $this->productRepo->attachClient($request);
        return self::showResponse($result);
    }

    public function detachClient(Request $request, $id)
    {
        $result = $this->productRepo->detachClient($request, $id);
        return self::showResponse($result);
    }
}
