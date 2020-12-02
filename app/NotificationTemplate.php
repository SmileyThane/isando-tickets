<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NotificationTemplate extends Model
{
    protected $fillable = ['name', 'description', 'text', 'notification_type_id', 'priority', 'entity_id', 'entity_type'];

    public function type(): HasOne
    {
        return $this->hasOne(NotificationType::class, 'id', 'notification_type_id');
    }

}
