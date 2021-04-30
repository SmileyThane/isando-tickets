<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientGroupHasClient extends Model
{
    protected $fillable = ['client_id', 'client_group_id'];
}
