<?php

namespace App\Http\Controllers\Api;

use App\Domains\Properties\Models\Property;

class PropertyApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new Property();
        $this->searchable = ['name', 'address'];
        $this->sorts = ['name'];
        $this->includes = ['units'];
        $this->validationRules = [];
    }
}
