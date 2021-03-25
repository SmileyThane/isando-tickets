<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'data'];

    public function getDataAttribute(): array
    {
        return json_decode($this->attributes['data'], true);
    }

    public function setDataAttribute(array $data = []): void
    {
        $this->attributes['data'] = json_encode($data);
    }
}
