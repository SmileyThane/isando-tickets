<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Activity extends Model
{
    protected $with = [
        'attachments',
    ];

    protected $fillable = ['title', 'content', 'model_id', 'model_type', 'type_id', 'client_id', 'company_user_id', 'datetime'];

    public function type(): HasOne
    {
        return $this->hasOne(ActivityType::class, 'id', 'type_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function getUpdatedAtAttribute()
    {
        return $this->attributes['updated_at'] ? Carbon::parse($this->attributes['updated_at'])->format('Y-m-d') : null;
    }
}
