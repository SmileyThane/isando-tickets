<?php


namespace App\Http\Controllers\API\Tracking\Traits;


trait Clients
{
    public function getClientList()
    {
        $result = $this->trackingProjectsRepo->getClients();
        return self::showResponse($result);
    }
}
