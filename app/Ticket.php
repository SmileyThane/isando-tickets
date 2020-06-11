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
        return $this->attributes['to_entity_type']::find($this->attributes['to_entity_id']);
    }

    public function getLastUpdateAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->calendar();
    }

    public function creator()
    {
        return $this->hasOne(CompanyUser::class, 'id', 'from_company_user_id');
    }

    public function contact()
    {
        return $this->hasOne(CompanyUser::class, 'id', 'contact_company_user_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'to_product_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'to_team_id');
    }

    public function priority()
    {
        return $this->hasOne(TicketPriority::class, 'id', 'priority_id');
    }

    public function status()
    {
        return $this->hasOne(TicketStatus::class, 'id', 'status_id');
    }
}
