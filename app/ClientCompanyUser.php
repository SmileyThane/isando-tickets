<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCompanyUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'client_id', 'company_user_id', 'description'];
    protected $hidden = ['updated_at', 'created_at'];

    public function clients()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function employee()
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }
}
