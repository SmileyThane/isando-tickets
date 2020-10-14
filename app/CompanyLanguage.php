<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyLanguage extends Model
{
    protected $fillable = ['company_id', 'language_id'];

    public function languageData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
