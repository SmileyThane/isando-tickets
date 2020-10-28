<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = ['entity_id', 'entity_type', 'address', 'address_type', 'address_line_2', 'address_line_3'];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type');
    }

}
