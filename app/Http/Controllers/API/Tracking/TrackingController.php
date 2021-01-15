<?php


namespace App\Http\Controllers\API\Tracking;


use Illuminate\Http\Request;

class TrackingController extends BaseController
{
    public function get(Request $request)
    {
        $tracking = $this->trackingRepo->all($request);
        return self::showResponse(true, $tracking);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->trackingRepo->validate($request);
        if ($result === true) {
            $result = $this->trackingRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }
}
