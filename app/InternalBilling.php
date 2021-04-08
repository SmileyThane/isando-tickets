<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InternalBilling extends Model
{
    protected $fillable = ['name', 'entity_id', 'entity_type', 'cost', 'currency_id'];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}
