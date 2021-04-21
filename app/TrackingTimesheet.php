<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrackingTimesheet extends Model
{
    const STATUS_TRACKED = 'tracked';
    const STATUS_PENDING = 'pending';
    const STATUS_UNSUBMITTED = 'unsubmitted';
    const STATUS_ARCHIVED = 'archived';

    protected $appends = [
        'total_time'
    ];

    protected $casts = [
        'is_manually' => 'boolean',
        'billable' => 'boolean'
    ];

    public function Project() {
        return $this->belongsTo(TrackingProject::class);
    }

    public function Times() {
        return $this->hasMany(TrackingTimesheetTime::class, 'timesheet_id', 'id');
    }

    public function getTotalTimeAttribute() {
        $items = $this->Times()->get();
        $total = 0;
        foreach ($items as $item) {
            $dateTime = $item->date . ' ' . $item->time;
            $start = Carbon::parse($dateTime)->startOfDay();
            $end = Carbon::parse($dateTime);
            $total += Carbon::parse($start)->diffInSeconds($end);
        }
        return $total; // in seconds
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($trackingTimesheet) {
            $trackingTimesheet->Times()->delete();
        });

    }

}
