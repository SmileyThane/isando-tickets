<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingProject extends Model
{
    protected $appends = [
        'tracked',
        'revenue'
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
        if (isset($this->rate) && is_numeric($this->rate)) {
            return number_format($this->tracked / 60 / 60 * $this->rate, 2);
        }
        return 0;
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($trackingProject) {
            $trackingProject->Trackers()->delete();
        });
    }
}
