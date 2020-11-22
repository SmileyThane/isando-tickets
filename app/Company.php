<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    public function getRegistrationDateAttribute()
    {
        return $this->attributes['registration_date'] ? Carbon::parse($this->attributes['registration_date'])->format('Y-m-d') : null;
    }

    public function employees(): HasMany
    {
        return $this->hasMany(CompanyUser::class, 'company_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(CompanyProduct::class, 'company_id', 'id');
    }

    public function clients(): MorphMany
    {
        return $this->morphMany(Client::class, 'supplier');
    }

    public function teams(): MorphMany
    {
        return $this->morphMany(Team::class, 'team_owner');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'entity');
    }

    public function phoneTypes(): MorphMany
    {
        return $this->morphMany(PhoneType::class, 'entity');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'entity');
    }

    public function addressTypes(): MorphMany
    {
        return $this->morphMany(AddressType::class, 'entity');
    }

    public function socials(): MorphMany
    {
        return $this->morphMany(Social::class, 'entity');
    }

    public function socialTypes(): MorphMany
    {
        return $this->morphMany(SocialType::class, 'entity');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'entity');
    }

    public function emailTypes(): MorphMany
    {
        return $this->morphMany(EmailType::class, 'entity');
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class,'company_id','id');
    }

    public function settings(): MorphOne
    {
        return $this->hasOne(CompanySettings::class, 'company_id', 'id');
    }

    public function getContactPhoneAttribute()
    {
        return $this->phones()->with('type')->first();
    }

    public function getContactEmailAttribute()
    {
        return $this->emails()->with('type')->first();
    }
}
