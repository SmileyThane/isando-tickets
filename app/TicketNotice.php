<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class TicketNotice extends Model
{
//    use SoftDeletes;

    protected $fillable = ['notice'];

    public function getCreatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['created_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
    }

    public function getUpdatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['updated_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id')->withTrashed();
    }
}

