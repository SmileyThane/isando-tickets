<?php

namespace App\Http\Controllers\API\Tracking;

use Illuminate\Http\Request;

class ProjectController extends BaseController
{

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

    public function toggleFavorite($id) {
        $result = $this->trackingProjectsRepo->toggleFavorite($id);
        return self::showResponse($result);
    }

}
