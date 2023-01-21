<?php

namespace App\Http\Controllers\Api;

use Insane\Journal\Models\Core\Category;

class CategoryApiController extends BaseController
{

  public function __construct()
    {
        $this->model = new Category();
        $this->searchable = ['name', 'alias'];
        $this->validationRules = [];
    }

}
