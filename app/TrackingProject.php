<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrackingProject extends Model
{
    static $STATUS_STARTED = 'started';
    static $STATUS_PAUSED = 'paused';
    static $STATUS_STOPPED = 'stopped';
    static $STATUS_ARCHIVED = 'archived';
    protected $appends = [
        'tracked',
        'revenue',
        'is_favorite',
        'name'
    ];
    protected $casts = [
        'is_favorite' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($trackingProject) {
            $trackingProject->Trackers()->delete();
            $trackingProject->Timesheet()->delete();
        });
    }

    public function Product()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function Client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function Team()
    {
        return $this->hasOne('App\Team', 'id', 'team_id');
    }

    public function Timesheet()
    {
        return $this->morphMany('App\TrackingTimesheet', 'entity');
    }

    public function getNameAttribute()
    {
//        $settings = TrackingSettings::where('entity_id', '=', Auth::user()->id)
//            ->where('entity_type', '=', User::class)
//            ->pluck('data')
//            ->first();
//        if (isset($settings['projectType'])) {
//            switch ($settings['projectType']) {
//                case 1: return $this->department ?? $this->project;
//                case 2: return $this->profit_center ?? $this->project;
//                default: return $this->project;
//            }
//        }
        return $this->project;
    }

    // passed time in seconds by project
    public function getTrackedAttribute()
    {
        return $this->Trackers()->get()->map(function ($item) {
            return $item->passed;
        })->sum();
    }

    public function Trackers()
    {
        return $this->morphMany('App\Tracking', 'entity')
            ->where('status', '<>', Tracking::$STATUS_ARCHIVED);
    }

    public function getRevenueAttribute()
    {
        $revenue = $this
            ->Trackers()
            ->get()
            ->map(function ($item) {
                return $item->revenue;
            })
            ->sum();
        return number_format($revenue, 2);
    }

    public function getIsFavoriteAttribute()
    {
        return (bool)UserFavorites::where([
            ['user_id', '=', Auth::user()->id],
            ['entity_type', '=', TrackingProject::class],
            ['entity_id', '=', $this->id]
        ])->first();
    }

    public function scopeMyTeams($query)
    {
        $teams = $teams = Team::whereHas('employees', function ($query) {
            return $query
                ->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->get();
        return $query->whereIn('team_id', '=', $teams->map(function ($team) {
            return $team->id;
        }));
    }

    public function scopeMyCompany($query)
    {
        $company = Auth::user()->employee->companyData()->with('employees.userData')->first();
        return $query->where('company_id', '=', $company->id);
    }
}
