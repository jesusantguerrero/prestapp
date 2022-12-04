<?php

namespace App\Http\Controllers;

use App\Domains\Properties\Models\Rent;
use Illuminate\Http\Request;
use Insane\Journal\Models\Invoice\Invoice;

class RentController extends InertiaController
{
    public function __construct(Rent $rent)
    {
        $this->model = $rent;
        $this->searchable = ['name'];
        $this->templates = [
            "index" => 'Rents/Index',
            "create" => 'Rents/LoanForm',
            "show" => 'Rents/Show'
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
        return Rent::createRent($postData);
    }

    protected function getEditProps(Request $request, $id)
    {
        return [
            'rents' => Rent::where('id', $id)->with(['client', 'invoices', 'paymentDocuments'])->first()
        ];
    }

    public function pay(Rent $rent, Invoice $invoice, Request $request) {
        $postData = $this->getPostData($request);
        if ($invoice->loan_id == $rent->id) {
            $rent->createPayment(array_merge($postData, [
                "client_id" => $rent->client_id,
                "documents" => [[
                    "payable_id" => $invoice->id,
                    "payable_type" => Invoice::class,
                    "amount" => $postData['amount']
                ]]
            ]));
            $rent->client->checkStatus();
        }
    }

    public function markAsPaid(Rent $rent, Invoice $invoice) {
        if ($invoice->resource_id == $rent->id) {
            $invoice->markAsPaid();
        }
    }
}
