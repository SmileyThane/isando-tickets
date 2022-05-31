<?php

namespace App;

use App\Repositories\CustomLicenseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomLicense extends Model
{

    protected $attributes = ['remote_client_id', 'client_id'];

    protected $appends = ['ixarma_object'];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function getIxarmaObjectAttribute()
    {
        $ixArmaId = $this->remote_client_id;
        if ($ixArmaId) {
            $result = (new CustomLicenseRepository())->makeIxArmaRequest("/api/v1/company/full/$ixArmaId", []);
            $parsedResult = json_decode($result->getContents(), true);
            if ($parsedResult && $parsedResult['status'] === 'SUCCESS') {
                if ($parsedResult['body'] &&
                    $parsedResult['body']['limits'] &&
                    $parsedResult['body']['limits']['expiresAt'] === "01/01/1970"
                ) {
                    $parsedResult['body']['limits']['expiresAt'] = Carbon::now()->subDay()->format('d/m/Y');
                }

                return $parsedResult['body'];
            }
        }

        return null;
    }
}
