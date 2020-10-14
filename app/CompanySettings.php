<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySettings extends Model
{
    protected $fillable = ['company_id', 'data'];

    public function getDataAttribute(): array
    {
        return (array) json_decode($this->attributes['data']);
    }

    public function setDataAttribute(array $data = [])
    {
        $this->attributes['data'] = json_encode($data);
    }
}
