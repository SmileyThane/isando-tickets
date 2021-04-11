<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'passed', 'service', 'entity', 'passed_decimal', 'revenue'
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
        return Carbon::parse($this->attributes['date_from'])->utc()->format('Y-m-d\TH:i:s.uP');
    }

    public function getDateToAttribute() {
        return Carbon::parse($this->attributes['date_to'])->utc()->format('Y-m-d\TH:i:s.uP');
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
        })->get();
        return $query->SimpleUser()
            ->orWhereIn('team_id', '=', $teams->map(function ($team) { return $team->id; }));
    }

    public function scopeCompanyAdmin($query) {
        $company = Auth::user()->employee->companyData()->with('employees.userData')->first();
        return $query->SimpleUser()
            ->orWhere('company_id', '=', $company->id);
    }
}
