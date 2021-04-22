<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function getManagedTeams(Request $request)
    {
        $teams = \App\Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->get();
        return self::showResponse((bool)$teams, $teams);
    }
}
