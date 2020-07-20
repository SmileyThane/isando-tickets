<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use SoftDeletes;

    protected $table = 'phones';

    protected $fillable = ['entity_id', 'entity_type', 'phone', 'phone_type'];

    public function phoneable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->hasOne(PhoneType::class, 'id', 'phone_type');
    }
}
