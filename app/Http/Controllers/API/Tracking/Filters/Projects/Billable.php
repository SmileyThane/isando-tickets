<?php

namespace App\Http\Controllers\API\Tracking\Filters\Projects;

use App\Http\Controllers\API\Tracking\Filters\Filter;

class Billable extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            return $builder->where('billable_by_default', '=', $values);
        }
        return $builder;
    }
}
