<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamCompanyUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'team_id', 'company_user_id'];
    protected $hidden = ['updated_at', 'created_at'];

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }
}
