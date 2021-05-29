<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use Illuminate\Http\Request;

trait Clients
{
    public function getClientList(Request $request)
    {
        $result = $this->clientRepo->clients($request);
        return self::showResponse((bool)$result, $result);
    }
}
