<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use Illuminate\Http\Request;

trait Tickets
{
    public function getTickets(Request $request)
    {
        $tickets = $this->trackingProjectsRepo->getTickets($request);
        return self::showResponse((bool)$tickets, $tickets);
    }
}
