<?php

namespace App\Domains\Core\Services;


// Us
use App\Domains\Core\Services\Neatlancer\NeatlancerClient;

class NeatlancerService {
    private $apiContext;
    private $accessToken;
    private $scope;
    private $tokenType;
    private $appId;
    private $expiresIn;
    private $nonce;
    private $client_id;
    private $secret;

    // Create a new instance with our paypal credentials
    public function __construct()
    {
        $this->setSettings();
        $this->setApiContext();
    }


    private function setSettings() {
        $this->client_id = config('atmosphere.sso.key');
        $this->secret = config('atmosphere.sso.secret');
    }

    static function getSettings() {
        $settings = [
            "client_id" => config('atmosphere.sso.key'),
            "secret" => config('atmosphere.sso.secret')
        ];
        return $settings;
    }

    public function createUser($data) {
        return $this->apiContext->user->store($data);
    }


    // api

    public function setApiContext() {
        $this->apiContext = new NeatlancerClient();
    }
}
