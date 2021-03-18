<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingProject extends Model
{
    protected $appends = [
        'tracked',
        'amount',
        'progress'
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

    public function getTrackedAttribute() {
        // TODO
        return 0;
    }

    public function getAmountAttribute() {
        // TODO
        return 0;
    }

    public function getProgressAttribute() {
        // TODO
        return 0;
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($trackingProject) {
            $trackingProject->Trackers()->delete();
        });
    }
}
