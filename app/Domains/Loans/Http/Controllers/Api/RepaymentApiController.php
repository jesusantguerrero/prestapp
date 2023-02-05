<?php

namespace App\Domains\Loans\Http\Controllers\Api;

use App\Domains\Loans\Models\LoanInstallment;
use App\Http\Controllers\Api\BaseController;

class RepaymentApiController extends BaseController
{
    public function __construct()
    {
        $this->model = new LoanInstallment();
        // $this->searchable = ['client_name'];
        // $this->sorts = ['address'];
        $this->validationRules = [];
    }
}
