<?php


namespace App\Http\Controllers\API\Tracking;

use App\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index(Request $request) {

        $from = Carbon::parse($request->get('periodStart', Carbon::now()))->format(Tracking::$DATETIME_FORMAT);
        $to = Carbon::parse($request->get('periodEnd', Carbon::now()))->format(Tracking::$DATETIME_FORMAT);

        $services = $this->trackingReportRepo->getTotalTimeByServices($from, $to);
        $projects = $this->trackingReportRepo->getTotalTimeByProjects($from, $to);
        $reports = $this->trackingReportRepo->getUserReports();
        $currentTrackings = $this->trackingRepo->getCurrentUserTracking();
        $topProjects = $this->trackingReportRepo->getTopProjects($from, $to, 5);
        $lastActivity = $this->trackingReportRepo->getLastActivity($from, $to);

        $period = null;
        return [
            'services' => $services,
            'projects' => $projects,
            'reports' => $reports,
            'tracking' => $currentTrackings,
            'topProjects' => $topProjects,
            'lastActivity' => $lastActivity,
        ];
    }
}
