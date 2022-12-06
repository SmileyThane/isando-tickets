<?php

namespace App;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tracking extends Model
{
    static $DATETIME_FORMAT = 'Y-m-d\TH:i:s';
    static $DATE_FORMAT = 'Y-m-d';
    static $STATUS_STARTED = 0;
    static $STATUS_STOPPED = 1;//'started';
    static $STATUS_PAUSED = 2;//'stopped';
    static $STATUS_ARCHIVED = 3;//'paused';
    protected $table = 'tracking';//'archived';
    protected $fillable = [
        'entity_id',
        'entity_type',
        'date_from',
        'date_to'
    ];

    protected $appends = [
        'passed', 'service', 'entity', 'passed_decimal', 'revenue', 'timesheet_status', 'readonly'
    ];

    public static function boot()
    {
        parent::boot();

        static::updated(function ($tracking) {

        });
    }

    public function User()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
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

    public function Tags()
    {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }

    public function getTimesheetStatusAttribute()
    {
        if ($this->Timesheet()->first()) {
            return $this->Timesheet()->first()->status;
        }
        return null;
    }

    public function Timesheet()
    {
        return $this->belongsTo(TrackingTimesheet::class, 'timesheet_id', 'id');
    }

    public function getReadonlyAttribute()
    {
        if ($this->status === self::$STATUS_ARCHIVED) {
            return true;
        }
        if (
            is_null($this->timesheet_status)
            || in_array($this->timesheet_status, [TrackingTimesheet::STATUS_TRACKED, TrackingTimesheet::STATUS_REJECTED])
        ) {
            return false;
        }
        return true;
    }

    public function getServiceAttribute()
    {
        return $this->Services()->first();
    }

    public function Services()
    {
        return $this->morphToMany(
            Service::class,
            'serviceable',
            'serviceable',
            'serviceable_id',
            'service_id'
        );
    }

    public function getPassedAttribute()
    {
        return Carbon::parse($this->attributes['date_from'])->diffInSeconds($this->status === self::$STATUS_STARTED ? now() : Carbon::parse($this->date_to));
    }

    public function getPassedDecimalAttribute()
    {
        try {
            return round($this->passed / 60 / 60 * 100) / 100;
        } catch (Exception $exception) {
            return 0;
        }
    }

    public function setPassedAttribute($value)
    {
        $this->attributes['passed'] = $value;
    }

    public function getDateFromAttribute()
    {
        return Carbon::parse($this->attributes['date_from'])->format(self::$DATETIME_FORMAT);
    }

    public function getDateToAttribute()
    {
        if ($this->attributes['date_to']) {
            return Carbon::parse($this->attributes['date_to'])->format(self::$DATETIME_FORMAT);
        }
        return null;
    }

    public function getRevenueAttribute()
    {
        if (!$this->billable) return 0;
        if (isset($this->rate) && !empty($this->rate)) {
            return round(($this->rate * $this->passed_decimal) * 100) / 100;
        }
        return 0;
    }

    public function scopeSimpleUser($query)
    {
        return $query->where('user_id', '=', Auth::user()->id);
    }

    public function scopeTeamManager($query)
    {
        $result = $query->SimpleUser();
        $teams = Team::query()->whereHas('employees', function ($query) {
            return $query
                ->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->select('id')->get();

        if ($teams) {
            $result->orWhereIn('team_id', $teams->pluck('id')->toArray());
        }

        return $result;
    }

    public function scopeCompanyAdmin($query)
    {
        $employee = Auth::user()->employee;
        $result = $query->SimpleUser();
        if ($employee->is_clientable === 0) {
            $result->orWhere('company_id', $employee->company_id);
        }

        return $result;

    }
}
