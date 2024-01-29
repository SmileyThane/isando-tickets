<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFilterGroupHasClients extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'group_id'];

    public function data()
    {
        return $this->hasOne(ClientFilterGroup::class, 'id', 'group_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'id', 'client_id');
    }
}
