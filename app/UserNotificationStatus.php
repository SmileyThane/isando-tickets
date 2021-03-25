<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserNotificationStatus extends Model
{
    public const NONE = 0;

    public const TICKET_NEW_ASSIGNED_TO_ME = 101;
    public const TICKET_NEW_ASSIGNED_TO_TEAM = 201;
    public const TICKET_NEW_ASSIGNED_TO_COMPANY = 301;

    public const TICKET_UPDATED_ASSIGNED_TO_ME = 102;
    public const TICKET_UPDATED_ASSIGNED_TO_TEAM = 202;
    public const TICKET_UPDATED_ASSIGNED_TO_COMPANY = 302;

    public const TICKET_CLIENT_RESPONSE_ASSIGNED_TO_ME = 103;


    protected $table = 'user_notification_statuses';

    protected $fillable = ['user_id', 'status'];

    public function userData(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
