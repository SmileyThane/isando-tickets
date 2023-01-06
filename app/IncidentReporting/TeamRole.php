<?php

namespace App\IncidentReporting;

class TeamRole extends ReferenceBook
{
    protected $table = 'incident_reporting_team_roles';

    public static function boot() {
        parent::boot();

        self::creating(function($model){
            if (!$model->position) {
                $model->position = StakeholderType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
