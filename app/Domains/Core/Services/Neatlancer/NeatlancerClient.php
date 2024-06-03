<?php

namespace App\Domains\Core\Services\Neatlancer;

use GuzzleHttp\Client;
use App\Domains\Core\Services\Neatlancer\Api\User;
// use Insane\Treasurer\Libraries\Paypal\Api\Plan;
// use Insane\Treasurer\Libraries\Paypal\Api\Product;
// use Insane\Treasurer\Libraries\Paypal\Api\Subscription;
use App\Domains\Core\Services\Neatlancer\Auth\ApiContext;

class NeatlancerClient
{
    public Client $apiContext;
    public User $user;

    public function __construct()
    {
        $this->client = new ApiContext(self::getSettings());
        $this->user = new User($this->client);
    }

    public static function getSettings()
    {
        $settings = [
            "client_id" => config('atmosphere.sso.key'),
            "secret" => config('atmosphere.sso.secret')
        ];
        return $settings;
    }
}
