<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'from_company_user_id'];
    protected $appends = ['from', 'to', 'last_update'];

    public function getFromAttribute()
    {
        return $this->attributes['from_entity_type']::find($this->attributes['from_entity_id']);
    }

    public function getToAttribute()
    {
        return $this->attributes['to_entity_type']::where('id',$this->attributes['to_entity_id'])->with('teams')->first();
    }

    public function getLastUpdateAttribute(): string
    {
        return Carbon::parse($this->attributes['updated_at'])->calendar();
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'from_company_user_id');
    }

    public function contact(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'contact_company_user_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Product::class, 'id', 'to_product_id');
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Team::class, 'id', 'to_team_id');
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
        return $this->hasMany(TicketAnswer::class, 'ticket_id', 'id');
    }

    public function histories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id');
    }

    public function notices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketNotice::class, 'ticket_id', 'id');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }
}
