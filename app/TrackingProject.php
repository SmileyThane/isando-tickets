<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrackingProject extends Model
{
    protected $appends = [
        'tracked',
        'amount',
        'progress'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(static function ($trackingProject) {
            $trackingProject->Trackers()->delete();
        });
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function trackers(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Tracking::class, 'entity');
    }

    public function getTrackedAttribute()
    {
        // TODO
        return 0;
    }

    public function getAmountAttribute()
    {
        // TODO
        return 0;
    }

    public function getProgressAttribute()
    {
        // TODO
        return 0;
    }
}
