<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddressType extends Model
{
    protected $fillable = ['name', 'name_de', 'icon', 'entity_id', 'entity_type'];
}
