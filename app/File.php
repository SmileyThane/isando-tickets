<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = ['id', 'model_id', 'name', 'filepath', 'model_type'];
    protected $appends = ['link'];

    public function getLinkAttribute(): string
    {
        return url('/') . Storage::url($this->attributes['filepath'] . $this->attributes['name']);
    }

    public function attacheable()
    {
        return $this->morphTo();
    }
}
