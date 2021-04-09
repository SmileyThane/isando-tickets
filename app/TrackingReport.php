<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackingReport extends Model
{
    use SoftDeletes;

    protected $table = 'tracking_reports';

    protected $casts = [
        'configuration' => 'array'
    ];
}
