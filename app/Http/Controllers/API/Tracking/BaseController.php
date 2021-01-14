<?php


namespace App\Http\Controllers\API\Tracking;


use App\Http\Controllers\API\Tracking\Traits\Clients;
use App\Http\Controllers\API\Tracking\Traits\Products;
use App\Http\Controllers\Controller;
use App\Repository\TrackingProjectRepository;

class BaseController extends Controller
{
    use Clients, Products;

    protected $trackingProjectsRepo;

    public function __construct(TrackingProjectRepository $trackingProjectRepository)
    {
        $this->trackingProjectsRepo = $trackingProjectRepository;
    }
}
