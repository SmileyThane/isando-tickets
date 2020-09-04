<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TicketAnswer extends Model
{
//    use SoftDeletes;
    protected $appends = ['created_at_time'];

    public function getCreatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->short_code;
        return Carbon::parse($this->attributes['created_at'])->locale($locale)->calendar();
    }

    public function getCreatedAtTimeAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->short_code;
        $createdAt = Carbon::parse($this->attributes['created_at']);
        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->locale($locale)->diffForHumans() : '';
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id')->withTrashed();
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

}
