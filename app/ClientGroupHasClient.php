<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientGroupHasClient extends Model
{
    protected $fillable = ['client_id', 'client_group_id'];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function clientGroup(): HasOne
    {
        return $this->hasOne(ClientGroup::class, 'id', 'client_group_id');
    }
}
