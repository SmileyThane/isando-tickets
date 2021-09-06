<?php

namespace App\Http\Controllers\API\Tracking\Filters\Coworkers;

use App\Http\Controllers\API\Tracking\Filters\Filter;
use App\TrackingProject;

class Projects extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            $trackingProjects = TrackingProject::whereIn('id', explode(',', $values))->select('company_id', 'team_id')
                ->get()
                ->unique(function($item) { return $item->company_id . $item->team_id; })->values()->all();
                $builder->where(function ($query) use ($trackingProjects) {
                    foreach ($trackingProjects as $project) {
                        $query->orWhere(function($subQuery) use ($project) {
                            $subQuery->orWhere('company_id', '=', $project->company_id);
                            if (!is_null($project->team_id)) {
                                $subQuery->whereHas('assignedToTeams', function ($q) use ($project) {
                                    $q->where('team_id', '=', $project->team_id);
                                });
                            }
                        });
                    }
                });
            return $builder;
        }
        return $builder;
    }
}
