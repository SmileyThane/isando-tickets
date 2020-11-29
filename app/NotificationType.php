<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'name_de', 'icon', 'entity_id', 'entity_type'];
}
