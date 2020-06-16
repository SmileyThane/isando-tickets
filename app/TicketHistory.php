<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHistory extends Model
{
//    use SoftDeletes;

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->calendar();

    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }
}
