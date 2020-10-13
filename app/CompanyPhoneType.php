<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyPhoneType extends Model
{
    protected $fillable = ['company_id', 'phone_type_id'];

    public function phoneTypeData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PhoneType::class, 'id', 'phone_type_id');
    }
}
