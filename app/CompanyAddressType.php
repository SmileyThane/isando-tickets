<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyAddressType extends Model
{
    protected $fillable = ['company_id', 'address_type_id'];

    public function addressTypeData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AddressType::class, 'id', 'address_type_id');
    }
}
