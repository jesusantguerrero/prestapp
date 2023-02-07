<?php

namespace App\Domains\Loans\Http\Controllers\Api;

use App\Domains\Loans\Models\LoanInstallment;
use App\Http\Controllers\Api\BaseController;
use Insane\Journal\Models\Core\PaymentDocument;

class PaymentApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new PaymentDocument();
        // $this->searchable = ['client_name'];
        // $this->sorts = ['address'];
        $this->validationRules = [];
        $this->includes = ['payable'];
        $this->filters = [
          'payable_type' => LoanInstallment::class
        ];
    }
}
