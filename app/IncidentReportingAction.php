<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentReportingAction extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function assignee()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
