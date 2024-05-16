<?php

namespace App\IncidentReporting;

class EventType extends ReferenceBook
{
    protected $table = 'incident_reporting_event_types';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->position) {
                $model->position = EventType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
