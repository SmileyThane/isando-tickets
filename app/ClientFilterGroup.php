<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFilterGroup extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name'];
}
