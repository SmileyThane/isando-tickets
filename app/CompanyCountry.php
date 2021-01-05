<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyCountry extends Model
{
    protected $fillable = ['company_id', 'country_id'];

    public function countryData(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
