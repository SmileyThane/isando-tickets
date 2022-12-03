<?php

namespace App\IncidentReporting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class ReferenceBook extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'company_id', 'name', 'name_de', 'position'];
}
