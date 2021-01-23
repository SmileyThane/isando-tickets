<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyLanguage extends Model
{
    protected $fillable = ['company_id', 'language_id'];

    public function languageData(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
