<?php

namespace App;

use App\Repositories\TrackingTimesheetRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrackingTimesheet extends Model
{
    const STATUS_TRACKED = 0;
    const STATUS_PENDING = 1;
    const STATUS_REJECTED = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_APPROVED = 4;
    const STATUS_UNSUBMITTED = 5;

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'total_time',
        'entity',
        'is_empty'
    ];

    protected $casts = [
        'is_manually' => 'boolean',
        'billable' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($trackingTimesheet) {
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

        static::deleting(function ($trackingTimesheet) {
//            if (!$trackingTimesheet->is_manually) {
//                Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->update(['timesheet_id' => null]);
//            } else {
            Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->delete();
//            }
            $trackingTimesheet->Times()->delete();
        });

        static::updating(function ($trackingTimesheet) {
            $trackings = Tracking::where('timesheet_id', '=', $trackingTimesheet->id)->get();
            foreach ($trackings as $tracking) {
                if (is_null($trackingTimesheet->service_id)) {
                    $tracking->Services()->sync([]);
                } else {
                    $tracking->Services()->sync([$trackingTimesheet->service_id]);
                }
            }
        });
    }

    public function getEntityAttribute()
    {
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

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Approver()
    {
        return $this->belongsTo(User::class);
    }

    public function Trackers()
    {
        return $this->hasMany(Tracking::class);
    }

    public function Service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getTotalTimeAttribute()
    {
        $items = $this->Times()->get();
        if (!count($items)) {
            $this->genTimes();
            $items = $this->Times()->get();
        }
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

    public function Times()
    {
        return $this->hasMany(TrackingTimesheetTime::class, 'timesheet_id', 'id');
    }

    public function genTimes()
    {
        for ($i = 0; $i <= 6; $i++) {
            $trackingTimesheetTime = new TrackingTimesheetTime();
            $trackingTimesheetTime->timesheet_id = $this->id;
            $trackingTimesheetTime->type = TrackingTimesheetTime::TYPE_WORK;
            $trackingTimesheetTime->date = Carbon::parse($this->from)->addDays($i)->format('Y-m-d');
            $trackingTimesheetTime->time = TrackingTimesheetRepository::convertSecondsToTimeFormat(0, true);
            $trackingTimesheetTime->save();
        }
    }

    public function getIsEmptyAttribute()
    {
        return $this->total_time === 0;
    }

    public function genNumber()
    {
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

    public function duplicate()
    {
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
