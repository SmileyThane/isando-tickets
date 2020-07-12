<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    public $timestamps = false;
    protected $table = 'model_has_roles';
    protected $fillable = ['model_id', 'model_type', 'role_id'];
}
