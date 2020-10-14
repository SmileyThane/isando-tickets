<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyCountry extends Model
{
    protected $fillable = ['company_id', 'country_id'];

    public function countryData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
