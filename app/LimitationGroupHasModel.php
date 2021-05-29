<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LimitationGroupHasModel extends Model
{
    protected $fillable = ['model_id', 'limitation_group_id', 'model_type'];


    public function clientGroup(): HasOne
    {
        return $this->hasOne(LimitationGroup::class, 'id', 'client_group_id');
    }
}
