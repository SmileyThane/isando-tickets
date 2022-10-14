<?php

namespace App\IncidentReporting;


class ActionBoardStatus extends ReferenceBook
{
    protected $table = 'incident_reporting_action_board_statuses';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->position) {
                $model->position = ActionType::where('company_id', $model->company_id)->max('position') + 1;
            }
        });
    }
}
