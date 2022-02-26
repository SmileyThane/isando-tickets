<?php

namespace App\IncidentReporting;

use App\IncidentReporting\ReferenceBook;

class FocusPriority extends ReferenceBook
{
    protected $table = 'incident_reporting_focus_priorities';

    public static function boot() {
        self::creating(function($model){
            if (!$model->position) {
                $model->position = FocusPriority::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
