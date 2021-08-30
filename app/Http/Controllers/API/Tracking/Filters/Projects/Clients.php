<?php

namespace App\Http\Controllers\API\Tracking\Filters\Projects;

use App\Http\Controllers\API\Tracking\Filters\Filter;

class Clients extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            return $builder->whereIn('client_id', explode(',', $values));
        }
        return $builder;
    }
}
