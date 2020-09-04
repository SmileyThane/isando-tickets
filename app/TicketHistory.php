<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TicketHistory extends Model
{
//    use SoftDeletes;

    public function getCreatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->short_code;
        return Carbon::parse($this->attributes['created_at'])->locale($locale)->calendar();
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id')->withTrashed();
    }
}
