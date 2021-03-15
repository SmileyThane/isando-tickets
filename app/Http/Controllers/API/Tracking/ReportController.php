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
        return $this->trackingReportRepo->generate($request);
    }

    public function callAction($method, $request)
    {
        $result = null;
        switch (strtolower($request[0]->query->get('format', 'json'))) {
            case 'pdf': return $this->trackingReportRepo->genPDF($request); break;
            case 'csv': return $this->trackingReportRepo->genCSV($request); break;
            default:
                try {
                    $result = parent::callAction($method, $request);
                    return self::showResponse(true, $result);
                } catch (\Exception $exception) {
                    return self::showResponse(false, $result);
                }
        }
    }

}
