<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Ticket extends Model
{
    use SoftDeletes;

    const ACTION_NEW_TICKET = 'new_ticket';
    const ACTION_UPDATE_TICKET = 'update_ticket';
    const ACTION_ATTACH_TEAM_TO_TICKET = 'attach_team_to_ticket';

    protected $fillable = [
        'id', 'number', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'from_company_user_id',
        'replicated_to_entity_id', 'replicated_to_entity_type', 'is_spam', 'sequence', 'merge_comment',
        'ticket_type_id', 'description', 'name', 'contact_company_user_id', 'to_company_user_id', 'to_team_id',
        'to_product_id', 'priority_id', 'status_id', 'due_date', 'connection_details', 'access_details', 'availability',
        'category_id', 'parent_id', 'unifier_id', 'merged_at'
    ];
    protected $appends = ['from', 'from_company_name', 'to', 'last_update', 'can_be_edited', 'can_be_answered',
        'replicated_to', 'ticket_type', 'created_at_time', 'merged_parent_info', 'merged_child_info', 'original_name'];
    protected $hidden = ['to'];

    protected $langId;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->langId = Auth::user() ? Auth::user()->language_id : 1;
    }

    protected static function booted()
    {
        static::created(static function ($ticket) {
            $last = Ticket::where('to_entity_type', $ticket->to_entity_type)
                ->where('to_entity_id', $ticket->to_entity_id)
                ->whereDate('created_at', date('Y-m-d'))->count();
            $ticket->sequence = $last;
            $ticket->save();
        });
    }

    public function getOriginalNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function getNameAttribute()
    {
        $translationsArray = Language::find($this->langId)->lang_map;
        $name = $this->attributes['name'];
        $name .= $this->parent_id ? ' [' . $translationsArray->ticket->merged_abbr . ']' : "";
        $name .= $this->is_spam === 1 ? " [SPAM]" : "";
        return $name;
    }

    public function getDescriptionAttribute()
    {
        return str_replace("\n", "<br>", $this->attributes['description']);
    }

    public function getFromAttribute()
    {
        return $this->attributes['from_entity_type']::where('id', $this->attributes['from_entity_id'])->withTrashed()->first();
    }

    public function getFromCompanyNameAttribute()
    {
        if ($this->from) {
            return $this->from->name;
        }
        return null;
    }

    public function getToAttribute()
    {
        return $this->attributes['to_entity_type']::where('id', $this->attributes['to_entity_id'])->withTrashed()->first();
    }

    public function getReplicatedToAttribute()
    {
        return $this->replicated_to_entity_type !== null ?
            $this->attributes['replicated_to_entity_type']::find($this->attributes['replicated_to_entity_id']) :
            null;
    }

    public function getCanBeEditedAttribute(): bool
    {
        return true;
        $roles = Auth::user()->employee->roles->pluck('id')->toArray();
        return count(array_intersect($roles, Role::HIGH_PRIVIGIES)) > 0;
    }

    public function getCanBeAnsweredAttribute(): bool
    {
        return true;
    }

    public function getLastUpdateAttribute(): string
    {
        $locale = Language::find($this->langId)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['updated_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
    }

    public function getCreatedAtAttribute()
    {
        try {
            $locale = Language::find($this->langId)->locale;
            $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
            return Carbon::parse($this->attributes['created_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();

        } catch (\Throwable $th) {
            Carbon::parse($this->attributes['created_at'])->calendar();
        }
    }

    public function getCreatedAtTimeAttribute()
    {
        $locale = Language::find($this->langId)->locale;
        $createdAt = Carbon::parse($this->attributes['created_at']);
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->locale($locale)->diffForHumans() : '';
    }

    public function creator(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'from_company_user_id')->withTrashed();
    }

    public function assignedPerson(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'to_company_user_id')->withTrashed();
    }

    public function contact(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'contact_company_user_id')->withTrashed();
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'to_product_id')->withTrashed();
    }

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'to_team_id')->withTrashed();
    }

    public function priority(): HasOne
    {
        return $this->hasOne(TicketPriority::class, 'id', 'priority_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(TicketCategory::class, 'id', 'category_id');
    }

    public function status(): HasOne
    {
        return $this->hasOne(TicketStatus::class, 'id', 'status_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TicketAnswer::class, 'ticket_id', 'id')
            ->orderBy('updated_at', 'desc');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id')
            ->orderBy('updated_at', 'desc');
    }

    public function notices(): HasMany
    {
        return $this->hasMany(TicketNotice::class, 'ticket_id', 'id')
            ->orderBy('updated_at', 'desc');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function mergedParent(): HasMany
    {
        return $this->hasMany(TicketMerge::class, 'child_ticket_id', 'id')
            ->with('parentTicketData');
    }

    public function mergedChild(): HasMany
    {
        return $this->hasMany(TicketMerge::class, 'parent_ticket_id', 'id')
            ->with('childTicketData');
    }

    public function trackers()
    {
        return $this->morphMany(Tracking::class, 'entity');
    }

    public function getMergedParentInfoAttribute(): string
    {
        if ($this->parent_id !== null && $this->unifier_id) {
            $translationsArray = Language::find($this->langId)->lang_map;
            $mergeComment = $translationsArray->ticket->ticket_merge_parent_msg;
            $unifiedUserName = User::find($this->unifier_id) ? User::find($this->unifier_id)->full_name : '';
            $mergeComment = str_replace(
                ['$ticket_number', '$ticket_subject', '$date', '$unifier'],
                [$this->number, $this->original_name, $this->merged_at, $unifiedUserName],
                $mergeComment
            );
            return $mergeComment;
        }
        return '';
    }

    public function getMergedChildInfoAttribute(): string
    {
        if ($this->parent_id !== null && $this->unifier_id) {
            $parentTicket = self::find($this->parent_id);
            if ($parentTicket) {
                $translationsArray = Language::find($this->langId)->lang_map;
                $mergeComment = $translationsArray->ticket->ticket_merge_child_msg;
                $unifiedUserName = User::find($this->unifier_id) ? User::find($this->unifier_id)->full_name : '';
                $mergeComment = str_replace(
                    ['$ticket_number', '$ticket_subject', '$date', '$unifier'],
                    [
                        $parentTicket->number, $parentTicket->original_name,
                        $this->merged_at, $unifiedUserName
                    ],
                    $mergeComment
                );
                return $mergeComment;
            }
        }
        return '';
    }

    public function childTickets(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderByDesc('id');
    }

    public function getNumberAttribute(): string
    {
        if ($this->attributes['number'] === null) {
            $this->setNumberAttribute();
            $this->save();
        }

        return $this->attributes['number'];
    }

    public function setNumberAttribute(): void
    {
        // Prefix + Delimiter + creation_date + Delimiter + sequence
        $settings = $owner = null;
        $ownerName = 'T';

        try {
            $owner = $this->to_enity_type === Company::class ?
                $this->to :
                $this->to->supplier_type::find($this->to->supplier_id);
        } catch (Throwable $th) {
            if (Auth::user()) {
                $owner = Auth::user()->employee->companyData; // as variant to modification or fix
            }
        }

        if ($owner) {
            $ownerName = $owner->name;
            $settings = $owner->settings;
        }

        if (!$settings || empty($settings->data['ticket_number_format']) ||
            count(explode('｜', $settings->data['ticket_number_format'])) != 5
        ) {
            $format = strtoupper(substr(str_replace(' ', '', $ownerName), 0, 6)) . '｜-｜YYYYMMDD｜-｜###';
        } else {
            $format = $settings->data['ticket_number_format'];
        }

        list($prefix, $delim1, $date, $delim2, $suffix) = explode('｜', $format);

        // prepare date format for PHP
        $date = str_replace(array('YYYY', 'YY', 'MM', 'DD'), array('Y', 'y', 'm', 'd'), $date);

        // prepare suffix for PHP
        $suffix = '%0' . strlen($suffix) . 'd';
        $this->attributes['number'] = $prefix .
            $delim1 . date($date, strtotime($this->attributes['created_at'])) .
            $delim2 . sprintf($suffix, $this->sequence);
    }

    public function getTicketTypeAttribute()
    {
        return TicketType::find($this->ticket_type_id);
    }

    public function billedBy(): HasOne
    {
        return $this->hasOne(InternalBilling::class, 'id', 'internal_billing_id');
    }

    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'ticket_followers',
            'ticket_id',
            'follower_id',
            'id',
            'id'
        );
    }
}
