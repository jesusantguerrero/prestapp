<?php

namespace App\Domains\Core\Services\Neatlancer\Auth;

use GuzzleHttp\Client;

class ApiContext {

    public Client $client;
    private $accessToken;
    private $tokenType;
    private $client_id;
    private $secret;
    private string $client_url;

    private const GRAND_TYPE = "client_credentials";

    public function __construct($options)
    {
        $this->client_url = config('atmosphere.sso.url');
        $this->client_id = $options['client_id'];
        $this->secret = $options['secret'];
        $this->client = $this->initClient();
    }

    public function getAccessToken() {
        $client = new Client();
        $result = $client->post($this->client_url . "/oauth/token", [
            "auth" => [$this->client_id, $this->secret],
            'form_params' => [
                'grant_type' => self::GRAND_TYPE
            ]
        ]);

        $body = json_decode($result->getBody());
        return $body;
    }

    public function setTokens($body) {
        $this->accessToken = $body->access_token;
        $this->tokenType =  "Bearer";
    }

    public function initClient() {
        $this->setTokens($this->getAccessToken());

        return new Client([
            "base_uri" => config('atmosphere.sso.url') . "/",
            "headers" => [
                "Authorization" => "$this->tokenType ". $this->accessToken
            ]
        ]);
    }
}
