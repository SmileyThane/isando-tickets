<?php

namespace App;

use App\IncidentReporting\ActionType;
use App\IncidentReporting\TeamRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncidentReportingAction extends Model
{
    const DEADLINE_TIME_INDICATOR = ['before', 'after'];
    const DEADLINE_TIME_PARAMETER = ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name', 'description',
        'deadline_time_parameter', 'deadline_time_value', 'deadline_time_indicator',
        'user_id', 'type_id', 'related_to_ir_ab_id'
    ];

    public function assignee(): HasOne
    {
        return $this->hasOne(TeamRole::class, 'id', 'user_id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(ActionType::class, 'id', 'type_id');
    }
}
