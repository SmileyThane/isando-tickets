<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingSettings extends Model
{
    protected $fillable = [
        'entity_id', 'entity_type', 'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
