<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'middle_name','title_before_name', 'title',
        'country_id', 'anredeform', 'language_id', 'timezone_id', 'settings', 'status'
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
        'full_name', 'contact_phone', 'contact_email', 'email'
    ];

    public function employee(): HasOne
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

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'entity');
    }

    public function emailTypes(): MorphMany
    {
        return $this->morphMany(EmailType::class, 'entity');
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function settings(): HasOne
    {
        return $this->morphOne(Settings::class, 'entity');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function getContactPhoneAttribute()
    {
        return $this->phones()->with('type')->first();
    }

    public function getEmailAttribute()
    {
        $email = $this->getContactEmailAttribute();
        return $email ? $email->email : null;
    }

    public function getContactEmailAttribute()
    {
        return $email = $this->emails()->with('type')->first();
    }

    public function emailSignatures(): MorphMany
    {
        return $this->morphMany(EmailSignature::class, 'entity');
    }

    public function notificationTemplates(): MorphMany
    {
        return $this->morphMany(NotificationTemplate::class, 'entity');
    }
}
