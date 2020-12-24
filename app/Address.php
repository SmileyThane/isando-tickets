<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use VIISON\AddressSplitter\AddressSplitter;
use CarpCai\AddressParser\Parser;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = ['entity_id', 'entity_type', 'address_type', 'street', 'street2', 'street3', 'city', 'postal_code', 'country_id'];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id','id');
    }
}
