<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientFilterGroup extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name'];

    public function children():HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
