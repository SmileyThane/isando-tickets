<?php


namespace App\Http\Controllers\API\Tracking;


use App\Http\Controllers\API\Tracking\Traits\Clients;
use App\Http\Controllers\API\Tracking\Traits\Products;
use App\Http\Controllers\Controller;
use App\Repository\TrackingRepository;
use App\Repository\TrackingProjectRepository;

class BaseController extends Controller
{
    use Clients, Products;

    protected $trackingRepo;
    protected $trackingProjectsRepo;

    public function __construct(
        TrackingRepository $trackingRepository,
        TrackingProjectRepository $trackingProjectRepository
    )
    {
        $this->trackingRepo = $trackingRepository;
        $this->trackingProjectsRepo = $trackingProjectRepository;
    }
}
