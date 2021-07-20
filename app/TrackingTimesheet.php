<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TrackingTimesheet extends Model
{
    const STATUS_TRACKED = 'tracked';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNSUBMITTED = 'unsubmitted';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'total_time',
        'entity'
    ];

    protected $casts = [
        'is_manually' => 'boolean',
        'billable' => 'boolean'
    ];

    public function getEntityAttribute() {
        if (isset($this->entity_type)) {
            if ($this->entity_type === TrackingProject::class) {
                return $this->entity_type::with('Client')
                    ->with('Product')
                    ->find($this->entity_id);
            }
            if ($this->entity_type === Ticket::class) {
                return $this->entity_type::with('assignedPerson')->find($this->entity_id);
            }
        }
        return null;
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

        static::retrieved(function($trackingTimesheet) {
//            if ($trackingTimesheet->Times()->count() < 7) {
////                $trackingTimesheet->Times()->delete();
//                $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
//                foreach ($daysOfWeek as $index => $dayOfWeek) {
//                    $time = new TrackingTimesheetTime();
//                    $time->type = TrackingTimesheetTime::TYPE_WORK;
//                    $time->date = Carbon::parse($trackingTimesheet->from)->addDays($index)->format('Y-m-d');
//                    $time->time = '00:00:00';
//                    $time->description = '';
//                    $time->timesheet_id = $trackingTimesheet->id;
//                    $time->save();
//                }
//            }
        });

        static::deleting(function($trackingTimesheet) {
            if (!$trackingTimesheet->is_manually) {
                Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->update(['timesheet_id' => null]);
            } else {
                Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->delete();
            }
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

    public function duplicate() {
        $new = $this->replicate();
        $new->from = Carbon::parse($new->from)->addWeek()->format('Y-m-d');
        $new->to = Carbon::parse($new->to)->addWeek()->format('Y-m-d');
        $new->status = TrackingTimesheet::STATUS_TRACKED;
        $new->push();
        $times = $this->times()->get();
        foreach ($times as $time) {
            $newTime = $time->replicate();
            $newTime->date = Carbon::parse($newTime->date)->addWeek()->format('Y-m-d');
            $newTime->timesheet_id = $new->id;
            $newTime->save();
        }
        return $new;
    }

}
