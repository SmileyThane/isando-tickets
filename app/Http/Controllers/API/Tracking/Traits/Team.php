<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait Team
{
    public function getCoworkers(Request $request)
    {
        $teams = $this->teamRepo->all($request);
        $coworkers = [];
//        foreach ($teams as $team) {
//            $users = $team->employees()->first()->employee()->first()->userData()->get();
//            $coworkers = array_merge($coworkers, $users->toArray());
//        }
        $coworkers = Auth::user()->employee->companyData()->first()
            ->employees()->whereDoesntHave('assignedToClients')
            ->where('is_clientable', false)
            ->with('userData')->get()
            ->map(function($user) {
                $user->userData->name = $user->userData->name . ' ' . $user->userData->surname;
                return $user->userData;
            });
        return self::showResponse((bool)COUNT($coworkers), $coworkers);
    }

    public function getManagedTeams(Request $request)
    {
        $teams = \App\Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        });
        if ($request->has('withEmployee') && $request->get('withEmployee') == true) {
            $teams->with('employees.employee.userData');
        }
        return self::showResponse((bool)$teams, $teams->get());
    }
}
