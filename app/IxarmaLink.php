<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IxarmaLink extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'login', 'password'];

    public function userData(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
