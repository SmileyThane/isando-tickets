<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyUserNotification extends Model
{
    protected $table = 'company_user_notifications';

    protected $fillable = ['company_id', 'user_id', 'ticket_notification_type_id'];

    public function userData(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function companyData(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

}
