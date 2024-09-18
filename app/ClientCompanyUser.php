<?php

namespace App;

use App\Contracts\ExternalSourceEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCompanyUser extends Model implements ExternalSourceEntity
{
    use SoftDeletes;

    protected $fillable = ['id', 'client_id', 'company_user_id', 'description'];
    protected $hidden = ['updated_at', 'created_at'];

    public function clients(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(CompanyUser::class, 'id', 'company_user_id');
    }

    public function externalSources(): MorphMany
    {
        return $this->morphMany(ExternalSource::class, 'entity');
    }
}
