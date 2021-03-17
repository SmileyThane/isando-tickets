<?php


namespace App\Http\Controllers\API\Tracking;


use App\Http\Controllers\API\Tracking\Traits\Clients;
use App\Http\Controllers\API\Tracking\Traits\Products;
use App\Http\Controllers\API\Tracking\Traits\Team;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TrackingReportRepository;
use App\Repositories\TrackingRepository;
use App\Repositories\TrackingProjectRepository;

class BaseController extends Controller
{
    use Clients, Products, Team;

    protected $trackingRepo;
    protected $trackingProjectsRepo;
    protected $teamRepo;
    protected $trackingReportRepo;

    public function __construct(
        TrackingRepository $trackingRepository,
        TrackingProjectRepository $trackingProjectRepository,
        TeamRepository $teamRepository,
        TrackingReportRepository $trackingReportRepository
    )
    {
        $this->trackingRepo = $trackingRepository;
        $this->trackingProjectsRepo = $trackingProjectRepository;
        $this->teamRepo = $teamRepository;
        $this->trackingReportRepo = $trackingReportRepository;
    }
}
