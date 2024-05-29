<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class TicketAnswer extends Model
{
//    use SoftDeletes;
    protected $fillable = ['answer', 'is_internal'];

    protected $appends = ['created_at_time', 'updated_at_time'];

    protected $casts = [
        'is_internal' => 'boolean'
    ];

    public function getCreatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['created_at'])->addHours($timeZoneDiff)->locale($locale);
    }

    public function getCreatedAtTimeAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $createdAt = Carbon::parse($this->attributes['created_at']);
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->locale($locale)->diffForHumans() : '';
    }

    public function getUpdatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['updated_at'])->addHours($timeZoneDiff)->locale($locale);
    }

    public function getUpdatedAtTimeAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $createdAt = Carbon::parse($this->attributes['updated_at']);
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->locale($locale)->diffForHumans() : '';
    }

    public function getAnswerAttribute()
    {
        $content = str_replace(["\n", "<br>", "<br/>"], "", $this->attributes['description']);
        $content = preg_replace("/<img[^cid:][^>]+\>/i", "[[image from attachments]]", $content);
        foreach ($this->attachments as $attachment)
        {
            $content = preg_replace('/\[\[image from attachments\]\]/', '<img src="' . $attachment->link . '"/>', $content, 1);
        }
        $content = preg_replace('/\[\[image from attachments\]\]/', '', $content);

        return $content;
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
