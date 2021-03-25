<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingLogger extends Model
{
    public const CREATE = 'create';
    public const ATTACH_TAGS = 'attach_tags';
    public const UPDATE_TAGS = 'update_tags';
    public const DETACH_TAGS = 'detach_tags';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const DUPLICATE = 'duplicate';

    protected $table = 'tracking_logger';
    protected $casts = [
        'from' => 'array',
        'to' => 'array'
    ];
}
