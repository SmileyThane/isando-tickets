<?php

namespace App\Http\Controllers\API\Tracking\Filters\Clients;

use App\Http\Controllers\API\Tracking\Filters\Filter;
use App\TrackingProject;

class Projects extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            return $builder->whereIn('id', TrackingProject::whereIn('id', explode(',', $values))->pluck('client_id')->unique());
        }
        return $builder;
    }
}
