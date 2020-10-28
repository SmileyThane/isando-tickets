<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'data'];

    public function getDataAttribute(): array
    {
        return (array) json_decode($this->attributes['data']);
    }

    public function setDataAttribute(array $data = [])
    {
        $this->attributes['data'] = json_encode($data);
    }
}
