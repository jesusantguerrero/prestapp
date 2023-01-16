<?php

namespace App\Http\Controllers\Api;

use App\Domains\Properties\Models\Rent;

class RentApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new Rent();
        // $this->searchable = ['client_name'];
        // $this->sorts = ['address'];
        $this->validationRules = [];
    }
}
