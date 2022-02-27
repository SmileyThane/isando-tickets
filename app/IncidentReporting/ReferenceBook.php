<?php

namespace App\IncidentReporting;

use Illuminate\Database\Eloquent\Model;

abstract class ReferenceBook extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'name', 'name_de', 'position'];
}
