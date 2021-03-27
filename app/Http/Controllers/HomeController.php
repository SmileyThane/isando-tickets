<?php

namespace App\Http\Controllers;

use App\Country;
use App\Repositories\EmailReceiverRepository;
use App\TimeZone;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    protected $emailReceiverRepo;

    /**
     * Create a new controller instance.
     *
     * @param EmailReceiverRepository $emailReceiverRepo
     */
    public function __construct(EmailReceiverRepository $emailReceiverRepo)
    {
        $this->emailReceiverRepo = $emailReceiverRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */

    public function index(): Renderable
    {
        return view('app');
    }

    public function receiveMail(): void
    {
        $this->emailReceiverRepo->receiveMail();
    }

    public function getTimeZones()
    {
        return self::showResponse(true, TimeZone::all());
    }
}
