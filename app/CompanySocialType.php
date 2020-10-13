<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySocialType extends Model
{
    protected $fillable = ['company_id', 'social_type_id'];

    public function socialTypeData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SocialType::class, 'id', 'social_type_id');
    }
}
