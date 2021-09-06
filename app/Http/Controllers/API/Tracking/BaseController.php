<?php


namespace App\Http\Controllers\API\Tracking;


use App\Http\Controllers\API\Tracking\Services\ClientService;
use App\Http\Controllers\API\Tracking\Traits\Clients;
use App\Http\Controllers\API\Tracking\Traits\Products;
use App\Http\Controllers\API\Tracking\Traits\Team;
use App\Http\Controllers\API\Tracking\Traits\Tickets;
use App\Http\Controllers\Controller;
use App\Repositories\TeamRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TrackingReportRepository;
use App\Repositories\TrackingRepository;
use App\Repositories\TrackingProjectRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\TrackingTimesheetRepository;

class BaseController extends Controller
{
    use Clients, Products, Team, Tickets;

    protected $trackingRepo;
    protected $trackingProjectsRepo;
    protected $teamRepo;
    protected $trackingReportRepo;
    protected $ticketRepo;
    protected $currencyRepo;
    protected $timesheetRepo;
    protected $clientService;

    public function __construct(
        TrackingRepository $trackingRepository,
        TrackingProjectRepository $trackingProjectRepository,
        TeamRepository $teamRepository,
        TrackingReportRepository $trackingReportRepository,
        TicketRepository $ticketRepository,
        CurrencyRepository $currencyRepository,
        TrackingTimesheetRepository $trackingTimesheetRepository,
        ClientService $clientServ
    )
    {
        $this->trackingRepo = $trackingRepository;
        $this->trackingProjectsRepo = $trackingProjectRepository;
        $this->teamRepo = $teamRepository;
        $this->trackingReportRepo = $trackingReportRepository;
        $this->ticketRepo = $ticketRepository;
        $this->currencyRepo = $currencyRepository;
        $this->timesheetRepo = $trackingTimesheetRepository;
        $this->clientService = $clientServ;
    }
}
