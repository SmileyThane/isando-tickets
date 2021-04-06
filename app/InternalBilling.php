<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InternalBilling extends Model
{

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}
