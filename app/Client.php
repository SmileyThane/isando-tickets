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

    protected $fillable = ['name', 'short_name', 'description', 'photo', 'city', 'country_id', 'is_active', 'company_external_id', 'logo_url'];

    protected $appends = [
        'contact_phone', 'contact_email', 'supplier_name'
    ];

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'model');
    }

    public function clientable(): MorphTo
    {
        return $this->morphTo();
    }

    public function allClients(): MorphMany
    {
        return $this->clients()->with('allClients');
    }

    public function clients(): MorphMany
    {
        return $this->morphMany(self::class, 'supplier');
    }

    public function teams(): MorphMany
    {
        return $this->morphMany(Team::class, 'team_owner');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(ClientCompanyUser::class, 'client_id', 'id');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'entity');
    }

    public function socials(): MorphMany
    {
        return $this->morphMany(Social::class, 'entity');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductClient::class, 'client_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function Projects()
    {
        return $this->hasMany(TrackingProject::class, 'client_id', 'id');
    }

    public function getSupplierNameAttribute()
    {
        if ($this->supplier_type && $this->supplier_id) {
            $supplier = $this->supplier_type::query()->find($this->supplier_id);
            if ($supplier) {
                return $supplier->name;
            }
        }

        return null;
    }

    public function getContactPhoneAttribute()
    {
        return $this->phones()->with('type')->first();
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'entity');
    }

    public function getContactEmailAttribute()
    {
        return $this->emails()->with('type')->first();
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'entity');
    }

    public function customLicense(): HasOne
    {
        return $this->hasOne(CustomLicense::class, 'client_id', 'id');
    }

    public function billing(): MorphMany
    {
        return $this->morphMany(InternalBilling::class, 'entity');
    }
}
