<?php

namespace App\IncidentReporting;

class ResourceType extends ReferenceBook
{
    protected $table = 'incident_reporting_resource_types';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->position) {
                $model->position = ResourceType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
