<?php

namespace App\IncidentReporting;

class ImpactPotential extends ReferenceBook
{
    protected $table = 'incident_reporting_impact_potentials';

    public static function boot() {
        self::creating(function($model){
            if (!$model->position) {
                $model->position = ImpactPotential::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
