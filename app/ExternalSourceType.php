<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalSourceType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function externalSources(): HasMany
    {
        return $this->hasMany(ExternalSource::class);
    }
}
