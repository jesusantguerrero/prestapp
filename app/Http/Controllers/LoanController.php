<?php

namespace App\Http\Controllers;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends InertiaController
{
    public function __construct(Loan $loan)
    {
        $this->model = $loan;
        $this->searchable = ['name'];
        $this->templates = [
            "index" => 'Loans/Index',
            "create" => 'Loans/LoanForm',
            "show" => 'Loans/Show'
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'interest_rate' => 'numeric|max:100',
            'installments' => 'array'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['client'];
        $this->filters = [];
        $this->resourceName= "loans";
        
    }

    protected function createResource(Request $request, $postData)
    {
        return LoanService::createLoan($postData, $request->get('installments'));
    }

    protected function getEditProps(Request $request, $id)
    {
        return [
            'loans' => Loan::where('id', $id)->with(['client', 'installments', 'paymentDocuments'])->first()
        ];
    }

    public function pay(Loan $loan, LoanInstallment $installment, Request $request) {
        $postData = $this->getPostData($request);
        if ($installment->loan_id == $loan->id) {
            $loan->createPayment(array_merge($postData, [
                "client_id" => $loan->client_id,
                "documents" => [[
                    "payable_id" => $installment->id,
                    "payable_type" => LoanInstallment::class,
                    "amount" => $postData['amount']
                ]]
            ]));
        }
    }

    public function markAsPaid(Loan $loan, LoanInstallment $installment) {
        if ($installment->loan_id == $loan->id) {
            $installment->markAsPaid();
        }
    }
}
