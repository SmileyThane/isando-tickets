<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Tracking extends Model
{
    protected $table = 'tracking';

    static $DATETIME_FORMAT = 'Y-m-d\TH:i:s';

    static $STATUS_STARTED = 'started';
    static $STATUS_PAUSED = 'paused';
    static $STATUS_STOPPED = 'stopped';
    static $STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'passed', 'service', 'entity', 'passed_decimal', 'revenue', 'timesheet_status', 'readonly'
    ];

    public function User() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

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

    public function Tags() {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }

    public function Services() {
        return $this->morphToMany(
            Service::class,
            'serviceable',
            'serviceable',
            'serviceable_id',
            'service_id'
        );
    }

    public function Timesheet() {
        return $this->belongsTo(TrackingTimesheet::class, 'timesheet_id', 'id');
    }

    public function getTimesheetStatusAttribute() {
        if ($this->Timesheet()->first()) {
            return $this->Timesheet()->first()->status;
        }
        return null;
    }

    public function getReadonlyAttribute() {
        if (is_null($this->timesheet_status) || in_array($this->timesheet_status, [TrackingTimesheet::STATUS_TRACKED, TrackingTimesheet::STATUS_REJECTED])) {
            return false;
        }
        return true;
    }

    public function getServiceAttribute() {
        return $this->Services()->first();
    }

    public function getPassedAttribute() {
        return Carbon::parse($this->date_from)->diffInSeconds($this->status !== 'stopped' ? now() : Carbon::parse($this->date_to));
    }

    public function getPassedDecimalAttribute() {
        try {
            return round($this->passed / 60 / 60 * 100) / 100;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    public function setPassedAttribute($value)
    {
        $this->attributes['passed'] = $value;
    }

    public function getDateFromAttribute() {
        return Carbon::parse($this->attributes['date_from'])->format(self::$DATETIME_FORMAT);
    }

    public function getDateToAttribute() {
        if ($this->attributes['date_to']) {
            return Carbon::parse($this->attributes['date_to'])->format(self::$DATETIME_FORMAT);
        }
        return null;
    }

    public function getRevenueAttribute() {
        if (!$this->billable) return 0;
        if (isset($this->rate) && !empty($this->rate)) {
            return round(($this->rate * $this->passed_decimal) * 100) / 100;
        }
        return 0;
    }

    public function scopeSimpleUser($query) {
        return $query->where('user_id', '=', Auth::user()->id);
    }

    public function scopeTeamManager($query) {
        $teams = $teams = Team::whereHas('employees', function ($query) {
            return $query
                ->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->get()->pluck('id')->toArray();
        return $query->SimpleUser()
            ->orWhereIn('team_id', $teams);
    }

    public function scopeCompanyAdmin($query) {
        $company = Auth::user()->employee()
            ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
            ->with('userData')->first();
        return $query->SimpleUser()
            ->orWhere('company_id', '=', $company->company_id);
    }
}
