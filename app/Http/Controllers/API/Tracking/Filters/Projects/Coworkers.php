<?php

namespace App\Http\Controllers\API\Tracking\Filters\Projects;

use App\Http\Controllers\API\Tracking\Filters\Filter;
use App\User;

class Coworkers extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            $coworkers = User::whereIn('id', explode(',', $values))
                ->with('employee.companyData')
                ->with('employee.assignedToTeams')
                ->get();
//            dd($coworkers);
            $builder->where(function($query) use ($coworkers) {
                foreach ($coworkers as $coworker) {
                    $query->orWhere(function ($subQuery) use ($coworker) {
                        $subQuery->where('company_id', '=', $coworker->employee->company_id);
                        if ($coworker->employee->assignedToTeams->count()) {
                            $subQuery->whereIn('team_id', $coworker->employee->assignedToTeams->pluck('team_id')->all());
                        }
                    });
                }
            });
            return $builder;
        }
        return $builder;
    }
}
