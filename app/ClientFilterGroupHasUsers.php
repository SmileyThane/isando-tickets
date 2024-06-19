<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFilterGroupHasUsers extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'group_id'];

    public function data()
    {
        return $this->hasOne(ClientFilterGroup::class, 'id', 'group_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
