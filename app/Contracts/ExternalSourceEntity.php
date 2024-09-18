<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ExternalSourceEntity
{
    public function externalSources(): MorphMany;
}
