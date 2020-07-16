<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    public function addressable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type');
    }
}
