<?php

namespace App\Http\Controllers;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Services\LoanService;
use App\Http\Requests\LoanStoreRequest;
use Illuminate\Http\Request;

class LoanController extends InertiaController
{
    public function __construct(Loan $loan)
    {
        $this->model = $loan;
        $this->searchable = ['name'];
        $this->templates = [
            "index" => 'Loans/Index',
            "create" => 'Loans/LoanForm'
        ];
        $this->validationRules = [
            'contact_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'interest_rate' => 'numeric|max:100',
            'installments' => 'array'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['contact'];
        $this->filters = [];
        $this->resourceName= "loans";
        
    }

    protected function createResource(Request $request, $postData)
    {
        return LoanService::createLoan($postData, $request->get('installments'));
    }
}
