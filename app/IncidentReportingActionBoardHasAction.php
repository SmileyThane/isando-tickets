<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentReportingActionBoardHasAction extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['order', 'action_id', 'action_board_id', 'action_type'];
}
