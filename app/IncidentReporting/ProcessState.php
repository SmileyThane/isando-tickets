<?php

namespace App\IncidentReporting;

class ProcessState extends ReferenceBook
{
    protected $table = 'incident_reporting_process_states';

    public static function boot() {
        parent::boot();

        self::creating(function($model){
            if (!$model->position) {
                $model->position = ProcessState::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
