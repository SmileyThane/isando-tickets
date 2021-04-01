<?php


namespace App\Http\Controllers\API\Tracking;


use Illuminate\Http\Request;

class ReportController extends BaseController
{

    public function generate(Request $request) {
        $result = $this->trackingReportRepo->validate($request, true);
        if ($result === false) {
            throw new \Exception('Validation error');
        }
        try {
            return $this->trackingReportRepo->generate($request);
        } catch (\Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }

    public function callAction($method, $request)
    {
        $result = null;
        try {
            switch (strtolower($request[0]->query->get('format', 'json'))) {
                case 'pdf': return $this->trackingReportRepo->genPDF(array_shift($request));
                case 'csv': return $this->trackingReportRepo->genCSV(array_shift($request));
                default:
                    $result = parent::callAction($method, $request);
                    return self::showResponse(true, $result);
            }
        } catch (\Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }

}
