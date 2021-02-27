<?php


namespace App\Repository;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Psr\Http\Message\StreamInterface;

class CustomLicenseRepository
{

    public function index($request)
    {
        $clients = (new ClientRepository())->getClients($request);
        if ($request->search) {
            $request['page'] = 1;
        }
        $clients = $clients->has('customLicense')->with('customLicense')->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
        return $clients->paginate($request->per_page ?? $clients->count());
    }

    public function makeIxArmaRequest($uri, $parameters, $method = 'GET', $withAuth = true): StreamInterface
    {
        $guzzle = new Client([
            'base_uri' => env('IXARMA_BASE_URL'),
        ]);
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhZG1pbi5peGFybWEiLCJ1cG4iOiJhZG1pbi5peGFybWEiLCJleHAiOjE2NDUyNzYxNjQsImFkZHJlc3MiOiIxMTExMTExMTExMTExMTExMTExMTExMTEiLCJncm91cHMiOlsiSVhBUk1BX0FETUlOIl0sImlhdCI6MTYxMzc0MDE2NCwianRpIjoiNDg1Mjk3ZDctZGZiNy00ZDE3LWI0NjItYjYzZDQ1OGJhNTk2In0.PreHkqWUoD4pTzcmck6Rq26V8BsOn7_tZMs_eqGCiJGViKBA3yf3tmz-61nU10JwRKMF34Koqpkx9ncS0x3lXCMreCfg8l8FDLj-qjCzmRP6dKvo-AlJsVN8xBGttfCMd9lHXogemLDb1kk7ta20Qx49S-dGMbFR5dCGmgBC3F7F05k3gW7YMDdTPBIMBv43_UbGMTa9q2316RjbPwtvjqFQlJgcdU6H7rquaETiZO8RERiyzYRA1pPv2-ZQgMilVSC1vC90xrd68CG3gJy6yr_5aAKR5zWlwcmIs4_9GYQH_3ex0FWRi9UtWEKiRyYG-r2Uxi0hlnCdBjDlGC8c5A';
        $response = $guzzle->request($method, $uri, [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $token],
            'Content-type' => 'application/json',
            'Accept' => '*/*',
            RequestOptions::JSON => $parameters
        ]);
        return $response->getBody();
    }

    public function find($id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $result = $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/limits", []);
        $parsedResult = json_decode($result->getContents(), true);
        if ($parsedResult['status'] === 'SUCCESS') {
            return $parsedResult['body'];
        }
        return null;
    }

    public function manageUsers($id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $result = $this->makeIxArmaRequest("/api/v1/app/user/$ixArmaId/page/0", []);
        $parsedResult = json_decode($result->getContents(), true);
        if ($parsedResult['status'] === 'SUCCESS') {
            return $parsedResult['body'];
        }
        return null;
    }

    public function itemHistory($id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $result = $this->makeIxArmaRequest("/api/v1/app/license/history/$ixArmaId/page/0", []);
        $parsedResult = json_decode($result->getContents(), true);
        if ($parsedResult['status'] === 'SUCCESS') {
            return array_reverse($parsedResult['body']);
        }
        return null;
    }

    public function create()
    {

    }

    public function update(Request $request, $id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $data = [
            'newAllowedUsers' => $request->usersAllowed,
            'newExpires' => Carbon::parse($request->expiresAt)->format('m/d/Y')
        ];
        $result = $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/limits", $data, 'PUT');
        if ($request->active === true) {
            $result = $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/renew", $data, 'GET');
        } else {
            $result = $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/suspend", $data, 'GET');
        }
        $parsedResult = json_decode($result->getContents(), true);
        if ($parsedResult['status'] === 'SUCCESS') {
            return $parsedResult['body'];
        } else {
            return $parsedResult['message'];
        }
    }

    public function delete()
    {

    }


}
