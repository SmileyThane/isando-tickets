<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrackingProject extends Model
{
    protected $appends = [
        'tracked',
        'revenue',
        'is_favorite'
    ];

    protected $casts = [
        'is_favorite' => 'boolean'
    ];

    public function Product() {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function Client() {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function Trackers() {
        return $this->morphMany('App\Tracking', 'entity');
    }

    // passed time in seconds by project
    public function getTrackedAttribute() {
        return $this->Trackers()->get()->map(function($item) {
            return $item->passed;
        })->sum();
    }

    public function getRevenueAttribute() {
        $revenue = $this
            ->Trackers()
            ->get()
            ->map(function($item) {
                return $item->revenue;
            })
            ->sum();
        return number_format($revenue, 2);
    }

    public function getIsFavoriteAttribute() {
        return (bool)UserFavorites::where([
            ['user_id', '=', Auth::user()->id],
            ['entity_type', '=', TrackingProject::class],
            ['entity_id', '=', $this->id]
        ])->first();
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($trackingProject) {
            $trackingProject->Trackers()->delete();
        });
    }

    public function scopeMyTeams($query) {
        $teams = $teams = Team::whereHas('employees', function ($query) {
            return $query
                ->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->get();
        return $query->whereIn('team_id', '=', $teams->map(function ($team) { return $team->id; }));
    }

    public function scopeMyCompany($query) {
        $company = Auth::user()->employee->companyData()->with('employees.userData')->first();
        return $query->where('company_id', '=', $company->id);
    }
}
