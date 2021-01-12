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
}
