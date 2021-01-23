<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SentNotification extends Model
{
    protected $fillable = ['subject', 'text', 'notification_type_id', 'priority', 'recipients', 'attachments', 'user_id', 'sent_at', 'entity_type', 'entity_id'];

    public function sender(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(NotificationType::class, 'id', 'notification_type_id');
    }

    public function getRecipientsAttribute(): array
    {
        return (array)json_decode($this->attributes['recipients']);
    }

    public function setRecipientsAttribute(array $data = [])
    {
        $this->attributes['recipients'] = json_encode($data);
    }

    public function getAttachmentsAttribute(): array
    {
        return (array)json_decode($this->attributes['attachments']);
    }

    public function setAttachmentsAttribute(array $data = [])
    {
        $this->attributes['attachments'] = json_encode($data);
    }

    public function getSentAtAttribute()
    {
        return $this->attributes['created_at'];
    }

    public function setSentAtAttribute($date)
    {
        return $this->attributes['created_at'] = $date;
    }

}
