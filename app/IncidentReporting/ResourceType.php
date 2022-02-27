<?php

namespace App\IncidentReporting;

use App\IncidentReporting\ReferenceBook;

class ResourceType extends ReferenceBook
{
    protected $table = 'incident_reporting_resource_types';

    public static function boot() {
        self::creating(function($model){
            if (!$model->position) {
                $model->position = ResourceType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
