<?php

namespace App\Http\Controllers\Api;

use Insane\Journal\Models\Core\TransactionLine;

class TransactionLineApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new TransactionLine();
        $this->searchable = ['concept'];
        $this->sorts = ['date'];
        $this->validationRules = [];
    }
}
