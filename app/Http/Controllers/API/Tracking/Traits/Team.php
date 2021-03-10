<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use Illuminate\Http\Request;

trait Team
{
    public function getCoworkers(Request $request)
    {
        $teams = $this->teamRepo->all($request);
        $coworkers = [];
        foreach ($teams as $team) {
            $users = $team->employees()->first()->employee()->first()->userData()->get();
            $coworkers = array_merge($coworkers, $users->toArray());
        }
        return self::showResponse((bool)COUNT($coworkers), $coworkers);
    }
}
