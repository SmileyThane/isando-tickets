<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name'
    ];

    public function Company() {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function Serviceable() {
        return $this->hasMany(Serviceable::class, 'service_id', 'id');
    }

    public function Tracking() {
        return $this->Serviceable()->first()->Tracking();
    }

}


