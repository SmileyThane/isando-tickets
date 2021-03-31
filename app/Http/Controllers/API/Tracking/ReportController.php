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
        switch (strtolower($request[0]->query->get('format', 'json'))) {
            case 'pdf': return $this->trackingReportRepo->genPDF(array_shift($request)); break;
            case 'csv': return $this->trackingReportRepo->genCSV(array_shift($request)); break;
            case 'html': return $this->trackingReportRepo->genPDF(array_shift($request), true); break;
            default:
                try {
                    $result = parent::callAction($method, $request);
                    return self::showResponse(true, $result);
                } catch (\Exception $exception) {
                    return self::showResponse(false, $exception->getMessage());
                }
        }
    }

}
