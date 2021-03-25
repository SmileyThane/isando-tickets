<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'passed', 'service', 'entity'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getEntityAttribute()
    {
        if (isset($this->entity_type)) {
            if ($this->entity_type === TrackingProject::class) {
                return $this->entity_type::with('Client')->with('Product')->find($this->entity_id);
            }
            if ($this->entity_type === Ticket::class) {
                return $this->entity_type::with('assignedPerson')->find($this->entity_id);
            }
        }
        return null;
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }

    public function services(): MorphToMany
    {
        return $this->morphToMany(
            Service::class,
            'serviceable',
            'serviceable',
            'serviceable_id',
            'service_id'
        );
    }

    public function getServiceAttribute()
    {
        return $this->Services()->first();
    }

    public function getPassedAttribute(): int
    {
        return Carbon::parse($this->date_to)->diffInSeconds(Carbon::parse($this->date_from));
    }

    public function getDateFromAttribute(): string
    {
        return Carbon::parse($this->attributes['date_from'])->utc()->format('Y-m-d\TH:i:s.uP');
    }

    public function getDateToAttribute(): string
    {
        return Carbon::parse($this->attributes['date_to'])->utc()->format('Y-m-d\TH:i:s.uP');
    }
}
