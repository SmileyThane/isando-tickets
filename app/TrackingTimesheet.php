<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrackingTimesheet extends Model
{
    const STATUS_TRACKED = 'tracked';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNSUBMITTED = 'unsubmitted';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_APPROVED = 'approved';

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

    public function User() {
        return $this->belongsTo(User::class);
    }

    public function Approver() {
        return $this->belongsTo(User::class);
    }

    public function Trackers() {
        return $this->hasMany(Tracking::class);
    }

    public function Service() {
        return $this->belongsTo(Service::class);
    }

    public function getTotalTimeAttribute() {
        $items = $this->Times()->get();
        $total = 0;
        foreach ($items as $item) {
//            $dateTime = $item->date . ' ' . $item->time;
//            $start = Carbon::parse($dateTime)->startOfDay();
//            $end = Carbon::parse($dateTime);
//            $total += Carbon::parse($start)->diffInSeconds($end);
            if ($item->time) {
                $time = explode(':', $item->time);
                $total += $time[0] * 60 * 60 + $time[1] * 60 + $time[2];
            }
        }
        return $total; // in seconds
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($trackingTimesheet) {
            Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->update(['timesheet_id' => null]);
            $trackingTimesheet->Times()->delete();
        });

        static::updating(function($trackingTimesheet) {
            $service = Service::where('id', '=', $trackingTimesheet->service_id)->first();
            if ($service) {
                $trackings = Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->get();
                foreach ($trackings as $tracking) {
                    $tracking->Services()->sync([$service->id]);
                }
            }
        });
    }

    public function genNumber() {
        $maxNumber = TrackingTimesheet::where([
            ['company_id', '=', $this->company_id]
        ])->max('number');
        if (!$maxNumber) {
            $maxNumber = 10000;
        } else {
            $maxNumber++;
        }
        return $maxNumber;
    }

}
