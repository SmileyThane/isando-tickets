<?php


namespace App\Http\Controllers\API\Tracking;

use App\Tracking;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TrackingController extends BaseController
{
    public function get(Request $request): JsonResponse
    {
        try {
            $tracking = $this->trackingRepo->all($request);

            return self::showResponse(true, $tracking);
        } catch (Throwable $throwable) {
            return self::showResponse(false, $throwable->getMessage());
        }
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->trackingRepo->validate($request, true);
        if ($result === true) {
            try {
                $result = $this->trackingRepo->create($request);
                $success = true;
            } catch (Exception $exception) {
                return self::showResponse(false, $exception->getMessage());
            }
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $trackingId)
    {
        $tracking = Tracking::find($trackingId);
        $result = null;
        try {
            $this->trackingRepo->validate($request, false);
            $result = $this->trackingRepo->update($request, $tracking);
        } catch (Throwable $exception) {
            dd($exception);
            return self::showResponse(false, $exception);
        }
        return self::showResponse(true, $result);
    }

    public function delete($trackingId)
    {
        $tracking = Tracking::find($trackingId);
        try {
            $result = $this->trackingRepo->delete($tracking);
            return self::showResponse($result);
        } catch (\Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }

    public function duplicate($trackingId)
    {
        $result = null;
        $tracking = Tracking::find($trackingId);
        if ($tracking) {
            $result = $this->trackingRepo->duplicate($tracking);
        }
        return self::showResponse((bool)$result, $result);
    }

}
