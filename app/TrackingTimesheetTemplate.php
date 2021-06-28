<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingTimesheetTemplate extends Model
{
    protected $fillable = [
        'user_id', 'name', 'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function User() {
        return $this->belongsTo(User::class);
    }
}
