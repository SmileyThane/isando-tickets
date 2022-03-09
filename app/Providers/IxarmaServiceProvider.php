<?php

namespace App\Providers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\ServiceProvider;

class IxarmaServiceProvider extends ServiceProvider
{

    protected $client;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->client = new GuzzleClient([
            'base_uri' => config('services.ixarma.endpoint'),
        ]);
    }

    protected function getHeaders($authToken = null, $data = [])
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $authToken,
                'fileType' => ''
            ],
            RequestOptions::JSON => $data,
            'Content-type' => 'application/json',
            'Accept' => '*/*',
        ];
    }

    public function login($userId, $ixarmaLogin, $ixarmaPassword)
    {
        $response = $this->client->post('auth', $this->getHeaders(null, [
            'username' => $ixarmaLogin,
            'password' => $ixarmaPassword,
        ]));
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $result = json_decode($response->getBody(), true);

        $response = $this->client->get('users/' . $result['id'], $this->getHeaders($result['token']));

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $result = json_decode($response->getBody(), true);

        $link = IxarmaLink::firstOrNew(['user_id' => $userId]);
        $link->login = $ixarmaLogin;
        $link->password = $result['password'];
        $link->save();

        return true;
    }

    public function isLoggedIn($userId) {
        $link = IxarmaLink::where('user_id', $userId)->first();
        return $link && !empty($link->password);
    }

    public function request($userId, $uri, $data, $method = 'GET', $withAuth = true) {
        $token = $withAuth ? $this->auth($userId) : null;

        $response = $this->client->request($method, $uri, $this->getHeaders($token, $data));
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return json_decode($response->getBody(), true);
    }

    protected function auth($userId) {
        if (!$this->isLoggedIn($userId)) {
            return false;
        }

        $link = IxarmaLink::where('user_id', $userId)->first();

        $response = $this->client->post('auth', $this->getHeaders(null, [
            'username' => $link->login,
            'password' => $link->password
        ]));
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $result = json_decode($response->getBody(), true);

        return $result['token'];
    }
}
