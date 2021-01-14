<?php


namespace App\Http\Controllers\API\Tracking;


use Illuminate\Support\Facades\Request;

class TrackingController extends BaseController
{
    public function get(Request $request) {
        return self::showResponse(true, []);
    }
}
