<?php

namespace App\Domains\Core\Services\Neatlancer\Api;

use App\Domains\Core\Services\Neatlancer\Auth\ApiContext;

class User {
    private const ENDPOINT = "api/admin/users";
    use ApiBase;

    public function __construct(ApiContext $apiContext)
    {
        $this->apiContext = $apiContext;
        $this->endpoint = self::ENDPOINT;
        $this->resultName = "users";
    }
}
