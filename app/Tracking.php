<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $appends = [
        'passed', 'service'
    ];

    public function User() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function Project() {
        return $this->hasOne('App\TrackingProject', 'id', 'project_id');
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
        return Carbon::parse($this->date_to)->diffInSeconds(Carbon::parse($this->date_from));
    }

    public function getDateFromAttribute() {
        return Carbon::parse($this->attributes['date_from'])->utc()->format('Y-m-d\TH:i:s.uP');
    }

    public function getDateToAttribute() {
        return Carbon::parse($this->attributes['date_to'])->utc()->format('Y-m-d\TH:i:s.uP');
    }
}
