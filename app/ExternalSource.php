<?php

namespace App;

use App\Casts\BCryptCaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExternalSource extends Model
{
    protected $fillable = [
        'domain',
        'domain_prefix',
        'uri',
        'name',
        'auth_name',
        'auth_method',
        'auth_headers',
        'password',
        'billing_price',
        'billing_currency',
        'billing_type',
        'is_billing_auto_renewal',
        'last_billed_at',
        'entity_id',
        'entity_type',
        'external_source_type_id'
    ];

    protected $casts = [
        'password' => BCryptCaster::class,
    ];

    protected $hidden = ['password', 'otp_secret'];

    protected $appends = ['is_otp_verified'];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function getIsOtpVerifiedAttribute(): bool
    {
        return $this->attributes['otp_secret'] !== null;
    }
    public function externalSourceType(): BelongsTo
    {
        return $this->belongsTo(ExternalSourceType::class);
    }

    public function getFullUrlAttribute(): string {
        $domain = trim($this->domain ?? '', '/');
        $domainPrefix = trim($this->domain_prefix ?? '', '/');
        $uri = trim($this->uri ?? '', '/');

        // Ensure domain ends with '/'
        if (!empty($domain) && !str_ends_with($domain, '/')) {
            $domain .= '/';
        }

        // Ensure domainPrefix starts with '/'
        if (!empty($domainPrefix) && !str_starts_with($domainPrefix, '/')) {
            $domainPrefix = '/' . $domainPrefix;
        }

        // Ensure URI starts with '/'
        if (!empty($uri) && !str_starts_with($uri, '/')) {
            $uri = '/' . $uri;
        }

        return $domain . $domainPrefix . $uri;
    }
}
