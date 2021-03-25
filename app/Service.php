<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name'
    ];

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function serviceable(): HasMany
    {
        return $this->hasMany(Serviceable::class, 'service_id', 'id');
    }

    public function tracking()
    {
        return $this->Serviceable()->first()->tracking();
    }
}
