<?php

namespace App\Http\Controllers\Api;

use App\Domains\CRM\Models\Client;

class ClientApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new Client();
        $this->searchable = ['names', 'lastnames', 'display_name', 'dni'];
        $this->sorts = ['names'];
        $this->validationRules = [];
    }
}
