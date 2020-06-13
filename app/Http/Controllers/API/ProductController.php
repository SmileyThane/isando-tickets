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

    public function get()
    {
        $clients = $this->productRepo->all();
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
        $result = $this->productRepo->validate($request);
        if ($result === true) {
            $result = $this->productRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete($id)
    {
        $result = $this->productRepo->delete($id);
        return self::showResponse($result);
    }

    public function attachEmployee(Request $request)
    {
        $result = $this->productRepo->attachEmployee($request);
        return self::showResponse($result);
    }

    public function detachEmployee($id)
    {
        $result = $this->productRepo->detachEmployee($id);
        return self::showResponse($result);
    }

    public function attachClient(Request $request)
    {
        $result = $this->productRepo->attachClient($request);
        return self::showResponse($result);
    }

    public function detachClient($id)
    {
        $result = $this->productRepo->detachClient($id);
        return self::showResponse($result);
    }
}
