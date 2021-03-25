<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class TicketAnswer extends Model
{
    protected $appends = ['created_at_time'];

    public function getCreatedAtAttribute(): string
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;

        return Carbon::parse($this->attributes['created_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
    }

    public function getCreatedAtTimeAttribute(): string
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $createdAt = Carbon::parse($this->attributes['created_at']);

        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->locale($locale)->diffForHumans() : '';
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id')->withTrashed();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

}
