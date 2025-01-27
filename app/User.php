<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    const ACTION_CREATE = 'create';
    const ACTION_RESTORE = 'restore';

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
        return $this->hasOne(CompanyUser::class, 'user_id', 'id')->withTrashed();
    }

    public function mainCompanies(): HasMany
    {
        return $this->hasMany(CompanyUser::class, 'user_id', 'id')->withTrashed();
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

    public function emailTypes(): MorphMany
    {
        return $this->morphMany(EmailType::class, 'entity');
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function settings(): MorphOne
    {
        return $this->morphOne(Settings::class, 'entity');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function getContactPhoneAttribute()
    {
        return $this->phones->first()?->loadMissing('type');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'entity');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'entity');
    }


    public function getEmailAttribute()
    {
        return $this->contact_email->email ?? null;
    }

    public function getEmailIdAttribute()
    {
        return $this->contact_email->id ?? null;
    }

    public function getContactEmailAttribute()
    {
        return $this->emails->sortBy('email_type')->first()?->loadMissing('type');
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
        return $this->hasMany(UserNotificationStatus::class, 'user_id', 'id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_users')
            ->with('settings');
    }

    public function getNumberAttribute()
    {
        if (!$this->companies->first()) {
            return $this->attributes['number'];
        }

        $settings = $this->companies->first()->settings;

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
        return $this->hasMany(TrackingReport::class, 'user_id', 'id');
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
        if ($this->settings && $this->settings->data) {
            return $this->settings->data['theme_bg_color'] ?? $this->settings->data['theme_color'] ?? 'grey darken-1';
        }
        return '';
    }

    public function ixarmaLink(): HasOne
    {
        return $this->hasOne(IxarmaLink::class, 'user_id', 'id');
    }

    /**
     * @param int $status
     * @return bool
     */
    public function hasNotificationStatus(int $status): bool
    {
        return $this->notificationStatuses->containsStrict('status', $status);
    }

    public function clientFilterGroups(): HasMany
    {
        return $this->hasMany(ClientFilterGroupHasUsers::class, 'user_id', 'id');
    }
}
