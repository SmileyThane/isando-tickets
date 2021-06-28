<?php

namespace App\Http\Controllers\API\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends BaseController
{

    public function get(Request $request) {
        $trackingProjects = $this->timesheetRepo->all($request);
        return self::showResponse(true, $trackingProjects);
    }

    public function getAllGroupedByStatus(Request $request) {
        $trackingProjects = $this->timesheetRepo->getAllGroupedByStatus($request);
        return self::showResponse(true, $trackingProjects);
    }

    public function find($id)
    {
        $trackingProjects = $this->timesheetRepo->find($id);
        return self::showResponse(true, $trackingProjects);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->timesheetRepo->validate($request);
        if ($result === true) {
            $result = $this->timesheetRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->timesheetRepo->validate($request);
        if ($result === true) {
            $result = $this->timesheetRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function remind(Request $request)
    {
        $success = false;
        $result = $this->timesheetRepo->validate($request);
        if ($result === true) {
            $result = $this->timesheetRepo->remind($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function submit(Request $request)
    {
        $success = false;
        $result = $this->timesheetRepo->validate($request);
        if ($result === true) {
            $result = $this->timesheetRepo->submit($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete($id)
    {
        $result = $this->timesheetRepo->delete($id);
        return self::showResponse((bool)$result, (bool)$result);
    }

    public function getCountTimesheetForApproval() {
        $count = $this->timesheetRepo->getCountTimesheetForApproval();
        return self::showResponse(true, [
            'count' => $count,
        ]);
    }

    public function copyLastWeek(Request $request) {
        $this->timesheetRepo->copyLastWeek(Auth::user());
        return self::showResponse(true, []);
    }
}
