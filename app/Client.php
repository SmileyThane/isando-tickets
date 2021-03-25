<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'short_name', 'description', 'photo', 'city', 'country_id', 'is_active',
        'company_external_id', 'logo_url'
    ];

    protected $appends = [
        'contact_phone', 'contact_email'
    ];

    public function clientable(): MorphTo
    {
        return $this->morphTo();
    }

    public function clients(): MorphMany
    {
        return $this->morphMany(self::class, 'supplier');
    }

    public function allClients(): MorphMany
    {
        return $this->clients()->with('allClients');
    }

    public function teams(): MorphMany
    {
        return $this->morphMany(Team::class, 'team_owner');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(ClientCompanyUser::class, 'client_id', 'id');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'entity');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'entity');
    }

    public function socials(): MorphMany
    {
        return $this->morphMany(Social::class, 'entity');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'entity');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductClient::class, 'client_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(TrackingProject::class, 'client_id', 'id');
    }

    public function getContactPhoneAttribute()
    {
        return $this->phones()->with('type')->first();
    }

    public function getContactEmailAttribute()
    {
        return $this->emails()->with('type')->first();
    }

    public function customLicense(): HasOne
    {
        return $this->hasOne(CustomLicense::class, 'client_id', 'id');
    }
}
