<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IncidentReportingActionBoard extends Model
{
    protected $hidden = ['priority_id', 'access_id', 'state_id', 'stage_monitoring_id', 'created_at', 'updated_at', 'deleted_at'];

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(
            IncidentReportingAction::class,
            'incident_reporting_action_board_has_actions',
            'action_board_id',
            'action_id'
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            IncidentReportingActionBoardCategory::class,
            'incident_reporting_action_board_has_categories',
            'action_board_id',
            'category_id'
        );
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'incident_reporting_action_board_has_clients',
            'action_board_id',
            'client_id'
        );
    }

    public function stageMonitoring()
    {
        return $this->hasOne(
            IncidentReportingActionBoardStageMonitoring::class,
            'id',
            'stage_monitoring_id'
        );
    }

    public function priority()
    {
        return $this->hasOne(
            IncidentReportingActionBoardPriority::class,
            'id',
            'priority_id'
        );
    }

    public function access()
    {
        return $this->hasOne(
            IncidentReportingActionBoardAccess::class,
            'id',
            'access_id'
        );
    }

    public function state()
    {
        return $this->hasOne(
            IncidentReportingActionBoardState::class,
            'id',
            'state_id'
        );
    }
}
