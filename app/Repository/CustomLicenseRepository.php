<?php


namespace App\Repository;


use App\IxarmaAuthorization;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\StreamInterface;
use Throwable;

class CustomLicenseRepository
{

    public function index($request)
    {
        $clients = (new ClientRepository())->getClients($request);
        if ($request->search) {
            $request['page'] = 1;
        }
        $clients = $clients->has('customLicense')
            ->with('customLicense')
            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
        return $clients->paginate($request->per_page ?? $clients->count());
    }

    public function find($id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $result = $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/limits", []);
        $parsedResult = json_decode($result->getContents(), true);
        return $parsedResult['status'] === 'SUCCESS' ? $parsedResult['body'] : null;
    }

    public function makeIxArmaRequest($uri, $parameters, $method = 'GET', $withAuth = true): ?StreamInterface
    {
        try {
            $guzzle = new Client([
                'base_uri' => env('IXARMA_BASE_URL'),
            ]);
            $ixArmaAuthorization = IxarmaAuthorization::where('company_id', Auth::user()->employee->company_id)->first();
            $token = $ixArmaAuthorization ? $ixArmaAuthorization->auth_token : '';
            $response = $guzzle->request($method, $uri, [
                RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $token],
                'Content-type' => 'application/json',
                'Accept' => '*/*',
                RequestOptions::JSON => $parameters
            ]);

            return $response->getBody();
        } catch (Throwable $throwable) {
            $this->ixArmaLogin();
            return null;
        }
    }

    private function ixArmaLogin()
    {
        $companyId = Auth::user()->employee->company_id;
        IxarmaAuthorization::where('company_id', $companyId)->delete();
        $data = ['login' => env('IXARMA_ADMIN_LOGIN'), 'password' => env('IXARMA_ADMIN_PASSWORD')];
        $result = $this->makeIxArmaRequest("/api/v1/user/login", $data, 'POST');
        $parsedResult = json_decode($result->getContents(), true);
        if ($parsedResult['status'] === 'SUCCESS') {
            $authObject = $parsedResult['body'];
            IxarmaAuthorization::create(['company_id' => $companyId, 'auth_token' => $authObject['token']]);

            return true;
        }

        return $parsedResult['message'];
    }

    public function getUsers($id)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $result = $this->makeIxArmaRequest("/api/v1/app/user/$ixArmaId/page/0", []);
        $parsedResult = json_decode($result->getContents(), true);
        return $parsedResult['status'] === 'SUCCESS' ? $parsedResult['body'] : null;
    }

    public function manageUser($id, $remoteUserId, $isLicensed)
    {
        $client = \App\Client::find($id);
        $ixArmaId = $client->customLicense->remote_client_id;
        $activationAction = $isLicensed === 'true' ? 'deactivate' : 'activate';
        $result = $this->makeIxArmaRequest("/api/v1/app/user/$ixArmaId/$remoteUserId/$activationAction", []);
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
        $parsedResult = json_decode($result->getContents(), true);
        if ($request->active === true) {
            $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/renew", $data, 'GET');
        } else {
            $this->makeIxArmaRequest("/api/v1/app/company/$ixArmaId/suspend", $data, 'GET');
        }

        return $parsedResult['status'] === 'SUCCESS' ? $parsedResult['body'] : $parsedResult['message'];
    }

    public function delete()
    {

    }


}
