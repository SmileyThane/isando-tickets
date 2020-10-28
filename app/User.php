<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'surname', 'title_before_name', 'title', 'country_id', 'anredeform', 'language_id', 'timezone_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name'
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'user_id', 'id');
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

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->surname;
    }


}
