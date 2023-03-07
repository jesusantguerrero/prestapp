<?php

namespace App\Domains\Properties\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Insane\Journal\Models\Invoice\Invoice;

class InvoiceApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new Invoice();
        // $this->searchable = ['client_name'];
        // $this->sorts = ['address'];
        $this->validationRules = [];
    }
}
