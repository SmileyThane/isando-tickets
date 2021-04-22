<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrackingTimesheetTime extends Model
{
    protected $table = 'tracking_timesheet_time';

    const TYPE_WORK = 'work';
    const TYPE_LUNCH = 'lunch';
    const TYPE_ABSENCE = 'absence';

    protected $fillable = [
        'type', 'date', 'time', 'description', 'timesheet_id'
    ];

    protected $appends = [
        'dayOfWeek',
        'dateTime'
    ];

    public function Timesheet() {
        return $this->belongsTo(TrackingTimesheet::class);
    }

    public function getDayOfWeekAttribute() {
        return (int)Carbon::parse($this->date)->format('N');
    }

    public function getDateTimeAttribute() {
        return $this->date . ' ' . $this->time;
    }
}
