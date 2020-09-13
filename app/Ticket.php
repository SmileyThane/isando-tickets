<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'from_company_user_id'];
    protected $appends = ['from', 'to', 'last_update', 'can_be_edited', 'can_be_answered'];
    protected $hidden = ['to'];

    public function getFromAttribute()
    {
        return $this->attributes['from_entity_type']::find($this->attributes['from_entity_id']);
    }

    public function getToAttribute()
    {
        return $this->attributes['to_entity_type']::where('id', $this->attributes['to_entity_id'])->with('teams', 'employees')->first();
    }

    public function getCanBeEditedAttribute()
    {
        $roles = Auth::user()->employee->roles->pluck('id')->toArray();
        return count(array_intersect($roles, Role::HIGH_PRIVIGIES)) > 0;
    }

    public function getCanBeAnsweredAttribute()
    {
        return true;
    }

    public function getLastUpdateAttribute(): string
    {
        $locale = Language::find(Auth::user()->language_id)->locale;
        $timeZoneDiff = TimeZone::find(Auth::user()->timezone_id)->offset;
        return Carbon::parse($this->attributes['updated_at'])->addHours($timeZoneDiff)->locale($locale)->calendar();
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'from_company_user_id')->withTrashed();
    }

    public function assignedPerson(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'to_company_user_id')->withTrashed();
    }

    public function contact(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'contact_company_user_id')->withTrashed();
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Product::class, 'id', 'to_product_id')->withTrashed();
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Team::class, 'id', 'to_team_id')->withTrashed();
    }

    public function priority(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TicketPriority::class, 'id', 'priority_id');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TicketStatus::class, 'id', 'status_id');
    }

    public function answers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketAnswer::class, 'ticket_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function histories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function notices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketNotice::class, 'ticket_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }
}
