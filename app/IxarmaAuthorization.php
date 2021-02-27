<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IxarmaAuthorization extends Model
{
    protected $fillable = ['company_id', 'auth_token'];
}
