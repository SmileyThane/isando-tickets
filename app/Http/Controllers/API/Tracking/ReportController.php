<?php


namespace App\Http\Controllers\API\Tracking;


use Illuminate\Http\Request;

class ReportController extends BaseController
{

    public function generate(Request $request) {
        $success = false;
        $result = $this->trackingReportRepo->validate($request, true);
        if ($result === true) {
            $result = $this->trackingReportRepo->generate($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

}
