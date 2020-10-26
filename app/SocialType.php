<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialType extends Model
{
    protected $fillable = ['name', 'name_de', 'icon', 'entity_id', 'entity_type'];
}
