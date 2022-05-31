<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingLogger extends Model
{
    const CREATE = 'create';
    const ATTACH_TAGS = 'attach_tags';
    const UPDATE_TAGS = 'update_tags';
    const DETACH_TAGS = 'detach_tags';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const DUPLICATE = 'duplicate';
    protected $table = 'tracking_logger';
    protected $casts = [
        'from' => 'array',
        'to' => 'array'
    ];
}
