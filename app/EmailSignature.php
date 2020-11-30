<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSignature extends Model
{
    protected $fillable = ['name', 'signature', 'entity_id', 'entity_type'];
}
