<?php


namespace App\Http\Controllers\API\Tracking;

use App\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index(Request $request) {

        $period = $request->get('period', 'lastWeek');

        switch ($period) {
            case 'thisWeek':
                $from = Carbon::now()->startOf('weeks')->format(Tracking::$DATETIME_FORMAT);
                $to = Carbon::now()->endOf('weeks')->format(Tracking::$DATETIME_FORMAT);
                break;
            case 'thisMonth':
                $from = Carbon::now()->startOf('month')->format(Tracking::$DATETIME_FORMAT);
                $to = Carbon::now()->endOf('month')->format(Tracking::$DATETIME_FORMAT);
                break;
            case 'thisQuarter':
                $from = Carbon::now()->startOf('quarter')->format(Tracking::$DATETIME_FORMAT);
                $to = Carbon::now()->endOf('quarter')->format(Tracking::$DATETIME_FORMAT);
                break;
            case 'thisYear':
                $from = Carbon::now()->startOf('year')->format(Tracking::$DATETIME_FORMAT);
                $to = Carbon::now()->endOf('year')->format(Tracking::$DATETIME_FORMAT);
                break;
            default:
                $from = Carbon::now()->startOfDay()->format(Tracking::$DATETIME_FORMAT);
                $to = Carbon::now()->endOfDay()->format(Tracking::$DATETIME_FORMAT);
                break;
        }

        $services = $this->trackingReportRepo->getTotalTimeByServices($from, $to);
        $projects = $this->trackingReportRepo->getTotalTimeByProjects($from, $to);
        $reports = $this->trackingReportRepo->getUserReports();
        $currentTrackings = $this->trackingRepo->getCurrentUserTracking();
        $topProjects = $this->trackingReportRepo->getTopProjects($from, $to, 5);

        $period = null;
        return [
            'services' => $services,
            'projects' => $projects,
            'reports' => $reports,
            'tracking' => $currentTrackings,
            'topProjects' => $topProjects,
        ];
    }
}
