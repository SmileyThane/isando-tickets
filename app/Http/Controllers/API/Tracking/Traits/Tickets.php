<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use Illuminate\Http\Request;

trait Tickets
{
    public function getTickets(Request $request)
    {
//        $tickets = $this->trackingProjectsRepo->getTickets($request);
        $tickets = $this->ticketRepo->all($request);
//        dd($tickets);
        return self::showResponse(true, $tickets);
    }
}
