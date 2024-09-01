<?php

namespace App\Domains\Properties\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Domains\Properties\Models\PropertyUnit;

class UnitApiController extends BaseController
{
    protected $authorizedUser = false;
    protected $authorizedTeam = false;

    public function __construct()
    {
        $this->model = new PropertyUnit();
        // $this->searchable = ['client_name'];
        $this->sorts = [];
        $this->validationRules = [];
        $this->includes = [];
        $this->filters = [
          "status" => PropertyUnit::STATUS_AVAILABLE
        ];
    }
}
