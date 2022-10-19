<?php

namespace App;

use App\IncidentReporting\ActionBoardStatus;
use App\IncidentReporting\EventType;
use App\IncidentReporting\FocusPriority;
use App\IncidentReporting\ImpactPotential;
use App\IncidentReporting\ProcessState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncidentReportingActionBoard extends Model
{

    const ACTION_BOARDS = 1;
    const SCENARIOS = 2;
    const IR = 3;


    protected $hidden = [
//        'priority_id', 'access_id', 'state_id', 'stage_monitoring_id',
        'created_at', 'deleted_at'
    ];

    protected $fillable = [
        'name', 'description', 'stage_monitoring_id',
        'priority_id', 'access_id', 'version',
        'parent_id', 'with_child_clients', 'state_id',
        'impact_potential_id', 'valid_till', 'updated_by', 'status_id',
        'type_id', 'source', 'occurred_on', 'detected_on' ,'reported_on'
    ];

    public function actions(): BelongsToMany
    {
        return $this->morphedByMany(
            IncidentReportingAction::class,
            'action',
            'incident_reporting_action_board_has_actions',
            'action_board_id',
            'action_id'
        );
    }

    public function actionBoards(): BelongsToMany
    {
        return $this->morphedByMany(
            self::class,
            'action',
            'incident_reporting_action_board_has_actions',
            'action_board_id',
            'action_id'
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            EventType::class,
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

    public function stageMonitoring(): HasOne
    {
        return $this->hasOne(
            IncidentReportingActionBoardStageMonitoring::class,
            'id',
            'stage_monitoring_id'
        );
    }

    public function priority(): HasOne
    {
        return $this->hasOne(
            FocusPriority::class,
            'id',
            'priority_id'
        );
    }

    public function access(): HasOne
    {
        return $this->hasOne(
            IncidentReportingActionBoardAccess::class,
            'id',
            'access_id'
        );
    }

    public function state(): HasOne
    {
        return $this->hasOne(
            ProcessState::class,
            'id',
            'state_id'
        );
    }

    public function status(): HasOne
    {
        return $this->hasOne(
            ActionBoardStatus::class,
            'id',
            'status_id'
        );
    }

    public function childVersions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderByDesc('id');
    }

    public function impactPotentials(): HasOne
    {
        return $this->hasOne(
            ImpactPotential::class,
            'id',
            'impact_potential_id'
        );
    }

    public function updatedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
