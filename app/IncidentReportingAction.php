<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentReportingAction extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    const DEADLINE_TIME_INDICATOR = ['before', 'after'];
    const DEADLINE_TIME_PARAMETER = ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'];

    public function assignee()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
