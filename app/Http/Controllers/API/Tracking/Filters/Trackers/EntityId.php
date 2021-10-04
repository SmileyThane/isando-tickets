<?php

namespace App\Http\Controllers\API\Tracking\Filters\Trackers;

use App\Http\Controllers\API\Tracking\Filters\Filter;

class EntityId extends Filter
{

    protected function applyFilters($builder)
    {
        $values = request($this->filterName());
        if (!is_null($values) && $values !== 'null') {
            return $builder->where('entity_id', '=', $values);
        }
        return $builder;
    }
}
