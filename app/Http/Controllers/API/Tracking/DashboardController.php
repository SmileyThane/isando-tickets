<?php


namespace App\Http\Controllers\API\Tracking;

use App\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index(Request $request) {

        $from = Carbon::parse($request->get('periodStart', Carbon::now()))->startOf('day')->format(Tracking::$DATETIME_FORMAT);
        $to = Carbon::parse($request->get('periodEnd', Carbon::now()))->endOf('day')->format(Tracking::$DATETIME_FORMAT);
        $team = $request->get('team', null);

        $services = $this->trackingReportRepo->getTotalTimeByServices($from, $to, $team);
        $projects = $this->trackingReportRepo->getTotalTimeByProjects($from, $to, $team);
//        $reports = $this->trackingReportRepo->getUserReports();
//        $currentTrackings = $this->trackingRepo->getCurrentUserTracking();
        $topProjects = $this->trackingReportRepo->getTopProjects($from, $to, 5, $team);
        $lastActivity = $this->trackingReportRepo->getLastActivity($from, $to, $team);

        $period = null;
        return [
            'services' => $services,
            'projects' => $projects,
//            'reports' => $reports,
//            'tracking' => $currentTrackings,
            'topProjects' => $topProjects,
            'lastActivity' => $lastActivity,
        ];
    }
}
