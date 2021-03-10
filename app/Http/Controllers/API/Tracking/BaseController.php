<?php


namespace App\Http\Controllers\API\Tracking;


use App\Http\Controllers\API\Tracking\Traits\Clients;
use App\Http\Controllers\API\Tracking\Traits\Products;
use App\Http\Controllers\API\Tracking\Traits\Team;
use App\Http\Controllers\Controller;
use App\Repository\ServiceRepository;
use App\Repository\TeamRepository;
use App\Repository\TrackingReportRepository;
use App\Repository\TrackingRepository;
use App\Repository\TrackingProjectRepository;

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
