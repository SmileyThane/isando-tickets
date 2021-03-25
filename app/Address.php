<?php

namespace App;

use CarpCai\AddressParser\Parser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use VIISON\AddressSplitter\AddressSplitter;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entity_id', 'entity_type', 'address_type', 'street', 'street2', 'street3', 'city', 'postal_code', 'country_id'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function type(): HasOne
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
