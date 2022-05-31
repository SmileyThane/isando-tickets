<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name'
    ];

    public function Company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function Tracking()
    {
        return $this->Serviceable()->first()->Tracking();
    }

    public function Serviceable()
    {
        return $this->hasMany(Serviceable::class, 'service_id', 'id');
    }

    public function Timesheets()
    {
        return $this->hasMany(TrackingTimesheet::class);
    }

    public function scopeMyCompany($query)
    {
        $company = Auth::user()->employee->companyData()->with('employees.userData')->first();
        return $query->where('company_id', '=', $company->id);
    }

}


