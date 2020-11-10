<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'from_company_user_id',
        'replicated_to_entity_id', 'replicated_to_entity_type', 'is_spam', 'sequence', 'merge_comment'];
    protected $appends = ['number', 'from', 'to', 'last_update', 'can_be_edited', 'can_be_answered', 'replicated_to'];
    protected $hidden = ['to'];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $last = Ticket::where('to_entity_type', $ticket->to_entity_type)
                ->where('to_entity_id', $ticket->to_entity_id)
                ->whereDate('created_at', '=', date('Y-m-d'))->max('sequence');
            $ticket->sequence = $last + 1;
        });
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'];
        $name .= $this->parent_id ? " [Merged]" : "";
        $name .= $this->is_spam === 1 ? " [SPAM]" : "";
        return $name;
    }

    public function getFromAttribute()
    {
        return $this->attributes['from_entity_type']::find($this->attributes['from_entity_id']);
    }

    public function getToAttribute()
    {
        return $this->attributes['to_entity_type']::find($this->attributes['to_entity_id']);
    }

    public function getReplicatedToAttribute()
    {
        return $this->replicated_to_entity_type !== null ?
            $this->attributes['replicated_to_entity_type']::find($this->attributes['replicated_to_entity_id']) :
            null;
    }

    public function getCanBeEditedAttribute(): bool
    {
        $roles = Auth::user()->employee->roles->pluck('id')->toArray();
        return count(array_intersect($roles, Role::HIGH_PRIVIGIES)) > 0;
    }

    public function getCanBeAnsweredAttribute(): bool
    {
        return true;
    }

    public function getLastUpdateAttribute(): string
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['updated_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
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

    public function childTickets(): HasMany
    {
//        return self::where('parent_id', $this->id);
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function getNumberAttribute(): string
    {
        // Prefix + Delimiter + creation_date + Delimiter + sequence
        try {
            $owner = $this->to_enity_type === Company::class ?
                $this->to :
                $this->to->supplier_type::find($this->to->supplier_id);
        } catch (\Throwable $th) {
            $owner = Auth::user()->employee->companyData; // as variant to modification or fix
        }

        $settings = $owner->settings;
        $format = $settings['ticket_number_format'];
        if (empty($format) || count(explode('｜', $format)) != 5) {
            $format = strtoupper(substr(str_replace(' ', '', $owner->name), 0, 6)) . '｜-｜YYYYMMDD｜-｜###';
        }

        list($prefix, $delim1, $date, $delim2, $suffix) = explode('｜', $format);
        // prepare date format for PHP
        $date = str_replace('YYYY', 'Y', $date);
        $date = str_replace('MM', 'm', $date);
        $date = str_replace('DD', 'd', $date);

        // prepare suffix for PHP
        $suffix = '%0' . strlen($suffix) . 'd';
        return $prefix . $delim1 . date($date) . $delim2 . sprintf($suffix, $this->sequence);
    }
}
