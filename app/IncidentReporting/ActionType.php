<?php

namespace App\IncidentReporting;

class ActionType extends ReferenceBook
{
    protected $table = 'incident_reporting_action_types';

    public static function boot() {
        self::creating(function($model){
            if (!$model->position) {
                $model->position = ActionType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
