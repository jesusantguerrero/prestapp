<?php

namespace App\Domains\Core\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class SSOService {
    private string $url;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
      $this->url = config('atmosphere.sso.url');
      $this->clientId = config('atmosphere.sso.key');
      $this->clientSecret = config('atmosphere.sso.secret');
    }

    public function authorize(string $state) {
        $query = http_build_query([
          "client_id" => $this->clientId,
          "redirect_uri" => config('app.url') . "/oauth/accept",
          "response_type" => "code",
          "scope" => "",
          "state" => $state
        ]);
        return redirect($this->url . "/oauth/authorize?" . $query);
    }

    public function acceptOath() {
        $response = Http::asForm()->post(
          config('atmosphere.sso.url') . "/oauth/token", [
          "grant_type" => "authorization_code",
          "client_id" => $this->clientId,
          "client_secret" => $this->clientSecret,
          "redirect_uri" => config('app.url') . "/oauth/accept",
          "code" => request()->code
        ]);

        session()->put($response->json());
    }

    public function getUser(string $token = null): array {

      $accessToken = $token ?? session('access_token');

      $response = Http::withHeaders([
          'Accept' => 'application/json',
          'Authorization' => 'Bearer ' . $accessToken,
      ])->get($this->url . '/api/user');

      return $response->json();
    }

    public function revokeToken(string $token = null): array {

      $accessToken = $token ?? session('access_token');

      $response = Http::withHeaders([
          'Accept' => 'application/json',
          'Authorization' => 'Bearer ' . $accessToken,
      ])->get($this->url . '/api/logout');

      return $response->json();
    }

    public function linkUser(array $userInfo): User {
      $user = User::updateOrCreate([
        'email' => $userInfo['email'],
      ], [
        'sso_name' => 'neatlancer',
        'sso_id' => $userInfo['id'],
        'name' => $userInfo['name'],
        'sso_token' => session("access_token"),
        'sso_refresh_token' => session("refresh_token"),
      ]);

      return $user;
    }


    public function addUser() {
        $accessToken = $token ?? session('access_token');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($this->url . '/api/logout');

        return $response->json();
      }
    }
}
