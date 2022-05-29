<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
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
        'number', 'name', 'surname', 'middle_name', 'title_before_name', 'title',
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
        'number', 'full_name', 'contact_phone', 'contact_email', 'email', 'email_id', 'avatar_url', 'color'
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
        $email = $this->emails()->orderBy('email_type')->first();
        return $email ? $email->email : null;
    }

    public function getEmailIdAttribute()
    {
        $email = $this->emails()->orderBy('email_type')->first();
        return $email ? $email->id : null;
    }

    public function getContactEmailAttribute()
    {
        return $email = $this->emails()->with('type')->orderBy('email_type')->first();
    }

    public function emailSignatures(): MorphMany
    {
        return $this->morphMany(EmailSignature::class, 'entity');
    }

    public function notificationTemplates(): MorphMany
    {
        return $this->morphMany(NotificationTemplate::class, 'entity');
    }

    public function language(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(Tracking::class, 'user_id', 'id');
    }

    public function notificationStatuses(): hasMany
    {
        return $this->hasMany(UserNotificationStatus::class,'user_id', 'id');
    }

    public function getNumberAttribute()
    {
        $employee = CompanyUser::where('user_id', $this->id)->first();
        if (!$employee) {
            return $this->attributes['number'];
        }

        $settings = $employee->companyData->settings;

        if (empty($settings->data['employee_number_format']) || count(explode('｜', $settings->data['ticket_number_format'])) != 4) {
            $format = '0||50000|8';
        } else {
            $format = $settings->data['employee_number_format'];
        }

        list($active, $prefix, $start, $size) = explode('|', $format);

        if (!$active) {
            return $this->attributes['number'];
        }

        return mb_strtoupper($prefix) . str_pad(($start + $this->attributes['id']), $size, '0', STR_PAD_LEFT);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->attributes['avatar_url'];
    }

    public function trackingReports() 
    {
        return $this->hasMany(TrackingReport::class,'user_id', 'id');
    }

    public function favoriteTrackingProjects() 
    {
        return $this->morphedByMany(
            TrackingProject::class,
            'entity',
            'user_favorites',
            'user_id',
            'entity_id',
            'id',
            'id'
        );
    }

    public function billing(): MorphMany
    {
        return $this->morphMany(InternalBilling::class, 'entity');
    }

    public function Timesheet() 
    {
        return $this->hasMany(TrackingTimesheet::class, 'user_id', 'id')
            ->with('Timesheet.Times');
    }

    public function getColorAttribute() 
    {
        $settings = Settings::where([
            ['entity_id', '=', $this->id],
            ['entity_type', '=', User::class]
        ])->first();
        if ($settings && $settings->data) {
            return $settings->data['theme_bg_color'] ?? $settings->data['theme_color'] ?? 'grey darken-1';
        }
        return '';
    }

    public function ixarmaLink(): HasOne
    {
        return $this->hasOne(IxarmaLink::class, 'user_id', 'id');
    }
}
