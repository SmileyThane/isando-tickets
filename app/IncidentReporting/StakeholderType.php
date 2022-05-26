<?php

namespace App\IncidentReporting;

class StakeholderType extends ReferenceBook
{
    protected $table = 'incident_reporting_stakeholder_types';

    public static function boot() {
        parent::boot();

        self::creating(function($model){
            if (!$model->position) {
                $model->position = StakeholderType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
