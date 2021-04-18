<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    public function planPrice(): HasMany
    {
        return $this->hasMany(PlanPrice::class, 'currency_id', 'id');
    }
}
