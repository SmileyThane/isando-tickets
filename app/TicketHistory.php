<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;


class TicketHistory extends Model
{
//    use SoftDeletes;

    protected $fillable = ['description', 'id', 'company_user_id', 'ticket_id'];

    public function getCreatedAtAttribute()
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['created_at'])->addHours($timeZoneDiff)->locale($locale);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id')->withTrashed();
    }

    public function getDescriptionAttribute()
    {
        $descriptionArray = json_decode($this->attributes['description'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $translationsArray = Language::find(Auth::user()->language_id)->lang_map;
            $historyTranslations = (array)$translationsArray->history_actions;
            $result = $historyTranslations[$descriptionArray['action']] . ' ';
            if ($descriptionArray['item'] !== null) {
                $translationGroup = $descriptionArray['translationGroup'];
                $item = $descriptionArray['item'];
                $result .= $descriptionArray['should_be_translated'] === true && isset($translationsArray->$translationGroup->$item) ?
                    $translationsArray->$translationGroup->$item :
                    $item;
            }
            return $result;
        }

        return $this->attributes['description'];
    }

}
