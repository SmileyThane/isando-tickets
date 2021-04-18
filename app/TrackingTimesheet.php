<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingTimesheet extends Model
{
    const STATUS_TRACKED = 'tracked';
    const STATUS_PENDING = 'pending';
    const STATUS_UNSUBMITTED = 'unsubmitted';
    const STATUS_ARCHIVED = 'archived';

    public function Project() {
        return $this->belongsTo(TrackingProject::class);
    }

    public function Times() {
        return $this->hasMany(TrackingTimesheetTime::class, 'timesheet_id', 'id');
    }

}
