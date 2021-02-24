<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serviceable extends Model
{

    protected $table = 'serviceable';

    public function Serviceable()
    {
        return $this->morphTo();
    }

    public function Service() {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
