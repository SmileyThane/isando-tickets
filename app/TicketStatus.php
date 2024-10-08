<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TicketStatus extends Model
{
    public const OPEN = 2;
    public const CLOSED = 5;


    protected $langId;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->langId = Auth::user() ? Auth::user()->language_id : 1;
    }

    public function getNameAttribute()
    {
        $translationsArray = Language::find($this->langId)->lang_map;
        $name = $this->attributes['name'];
        return $translationsArray->ticket_statuses->$name ?? $this->attributes['name'];
    }
}
