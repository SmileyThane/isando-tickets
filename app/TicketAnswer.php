<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketAnswer extends Model
{
//    use SoftDeletes;
    protected $appends = ['created_at_time'];

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->calendar();
    }

    public function getCreatedAtTimeAttribute()
    {
        $createdAt = Carbon::parse($this->attributes['created_at']);
        return $createdAt->diffInDays(now()) <= 1 ? $createdAt->diffForHumans() : '';
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

}
