<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrackingTimesheetTime extends Model
{
    const TYPE_WORK = 'work';
    const TYPE_LUNCH = 'lunch';
    const TYPE_ABSENCE = 'absence';
    protected $table = 'tracking_timesheet_time';
    protected $fillable = [
        'type', 'date', 'time', 'description', 'timesheet_id'
    ];

    protected $appends = [
        'dayOfWeek',
        'dateTime',
        'timeInSec',
    ];

    public function Timesheet()
    {
        return $this->belongsTo(TrackingTimesheet::class);
    }

    public function getDayOfWeekAttribute()
    {
        return (int)Carbon::parse($this->date)->format('N');
    }

    public function getDateTimeAttribute()
    {
        return $this->date . ' ' . $this->time;
    }

    public function getTimeInSecAttribute()
    {
        $time = explode(':', $this->time);
        $hour = $time[0];
        $minutes = $time[1];
        $seconds = $time[2];
        return ($hour * 60 * 60) + ($minutes * 60) + $seconds;
    }
}
