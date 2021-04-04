<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $fillable = [
        'entity_id',
        'entity_type'
    ];

    protected $appends = [
        'passed', 'service', 'entity', 'passed_decimal', 'revenue'
    ];

    public function User() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function getEntityAttribute() {
        if (isset($this->entity_type)) {
            if ($this->entity_type === TrackingProject::class) {
                return $this->entity_type::with('Client')
                    ->with('Product')
                    ->find($this->entity_id);
            }
            if ($this->entity_type === Ticket::class) {
                return $this->entity_type::with('assignedPerson')->find($this->entity_id);
            }
        }
        return null;
    }

    public function Tags() {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }

    public function Services() {
        return $this->morphToMany(
            Service::class,
            'serviceable',
            'serviceable',
            'serviceable_id',
            'service_id'
        );
    }

    public function getServiceAttribute() {
        return $this->Services()->first();
    }

    public function getPassedAttribute() {
        return Carbon::parse($this->date_to)->diffInSeconds(Carbon::parse($this->date_from));
    }

    public function getPassedDecimalAttribute() {
        return floor($this->passed / 60 / 60 * 100) / 100;
    }

    public function setPassedAttribute($value)
    {
        $this->attributes['passed'] = $value;
    }

    public function getDateFromAttribute() {
        return Carbon::parse($this->attributes['date_from'])->utc()->format('Y-m-d\TH:i:s.uP');
    }

    public function getDateToAttribute() {
        return Carbon::parse($this->attributes['date_to'])->utc()->format('Y-m-d\TH:i:s.uP');
    }

    public function getRevenueAttribute() {
        if (!$this->billable) return 0;
        if (isset($this->rate) && !empty($this->rate)) {
            return number_format($this->rate * $this->passed_decimal, 2);
        }
        return 0;
    }
}
