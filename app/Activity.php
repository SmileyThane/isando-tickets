<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Activity extends Model
{

    protected $fillable = ['title', 'content', 'model_id', 'model_type', 'type_id', 'company_user_id', 'datetime'];

    public function type(): HasOne
    {
        return $this->hasOne(ActivityType::class, 'id', 'type_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }
}