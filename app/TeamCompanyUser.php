<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamCompanyUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'team_id', 'company_user_id', 'is_manager'];
    protected $hidden = ['updated_at', 'created_at'];

    protected $casts = [
        'is_manager' => 'boolean'
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function teamData(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }
}
