<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    const INTERNAL = 4;

    protected $fillable = ['name', 'name_de', 'icon', 'entity_id', 'entity_type'];
}
