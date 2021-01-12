<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\TrackingProjectRepository;
use Illuminate\Http\Request;

class TrackingController extends Controller
{

    protected $trackingProjectsRepo;

    public function __construct(TrackingProjectRepository $trackingProjectRepository)
    {
        $this->trackingProjectsRepo = $trackingProjectRepository;
    }

    public function get(Request $request) {
        $trackingProjects = $this->trackingProjectsRepo->all($request);
        return self::showResponse(true, $trackingProjects);
    }

    public function find($id)
    {
        $trackingProjects = $this->trackingProjectsRepo->find($id);
        return self::showResponse(true, $trackingProjects);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->trackingProjectsRepo->validate($request);
        if ($result === true) {
            $result = $this->trackingProjectsRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->trackingProjectsRepo->validate($request);
        if ($result === true) {
            $result = $this->trackingProjectsRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete($id)
    {
        $result = $this->trackingProjectsRepo->delete($id);
        return self::showResponse($result);
    }

    public function getClientList()
    {
        $result = $this->trackingProjectsRepo->getClients();
        return self::showResponse($result);
    }

    public function getProductList()
    {
        $result = $this->trackingProjectsRepo->getProducts();
        return self::showResponse($result);
    }
}
