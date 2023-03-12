<?php

namespace App\Domains\Properties\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

class PaymentApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new Payment();
        // $this->searchable = ['client_name'];
        $this->sorts = ['payment_date'];
        $this->validationRules = [];
        $this->includes = ['payable', 'payable.client', 'account'];
        $this->filters = [
          'payable_type' => Invoice::class
        ];
    }
}
