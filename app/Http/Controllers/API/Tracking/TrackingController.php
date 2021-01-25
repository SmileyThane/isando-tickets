<?php


namespace App\Http\Controllers\API\Tracking;


use App\Tracking;
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
        $result = $this->trackingRepo->validate($request, true);
        if ($result === true) {
            $result = $this->trackingRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, Tracking $tracking)
    {
        $success = false;
        $result = $this->trackingRepo->validate($request, false);
        if ($result === true) {
            $result = $this->trackingRepo->update($request, $tracking);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete(Tracking $tracking)
    {
        $result = $this->trackingRepo->delete($tracking);
        return self::showResponse($result);
    }

    public function duplicate(Tracking $tracking)
    {
        $result = $this->trackingRepo->duplicate($tracking);
        return self::showResponse((bool)$result, $result);
    }

}
