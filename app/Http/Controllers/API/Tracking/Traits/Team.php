<?php


namespace App\Http\Controllers\API\Tracking\Traits;

use App\Company;
use App\Http\Controllers\API\Tracking\Filters\Coworkers\Projects;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;

trait Team
{
    public function getCoworkers(Request $request)
    {
        $coworkers = [];
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS)) {
            $coworkers = Auth::user()->employee->companyData()->first()
                ->employees()->whereDoesntHave('assignedToClients')
                ->where('is_clientable', false)
                ->with('userData');
            $coworkers = app(Pipeline::class)
                ->send($coworkers)
                ->through([
                    Projects::class,
                ])
                ->thenReturn();
            $coworkers = $coworkers->get()
                ->map(function ($user) {
                    return $user->userData;
                });
        } elseif (Auth::user()->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS)) {
            $teams = $this->teamRepo->all($request);
            foreach ($teams as $team) {
                $users = $team->employees()->first()->employee()->first()->userData()->get();
                $coworkers = array_merge($coworkers, $users->toArray());
            }
        } else {
            $coworkers[] = Auth::user()->employee->userData;
        }

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

    public function getManagersOfTeams(Request $request)
    {
        $teams = \App\Team::query()
            ->whereHas('employees', function ($query) {
                return $query->where('company_user_id', '=', Auth::user()->employee->id);
            })
            ->with('employees.employee.userData')
            ->get();

        $teamManagers = collect();
        foreach ($teams as $team) {
            $managers = $team->employees->filter(function ($item) {
                return $item->is_manager;
            });
            if ($managers->count()) {
                $items = $team->employees->filter(function ($item) {
                    return $item->is_manager;
                })
                    ->map(function ($item) {
                        return $item->employee->userData;
                    });
                $teamManagers = $teamManagers->merge($items);
            }
        }

        return self::showResponse((bool)$teams, $teamManagers->unique()->all());
    }
}
