<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
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
            "create" => 'Rents/RentForm',
            "show" => 'Rents/Show'
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'commission' => 'numeric|max:100',
            'installments' => 'array'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['client', 'property'];
        $this->filters = [];
        $this->resourceName= "loans";

    }

    public function create(Request $request) {
      $teamId = $request->user()->current_team_id;

      return inertia($this->templates['create'], [
        'rents' => null,
        'properties' => PropertyService::ofTeam($teamId),
        'clients' => ClientService::ofTeam($teamId),
      ]);
    }

    protected function createResource(Request $request, $postData)
    {
        return PropertyService::createRent($postData);
    }

    protected function getEditProps(Request $request, $id)
    {
        return [
            'rents' => Rent::where('id', $id)->with(['client', 'invoices', 'transaction'])->first()
        ];
    }

    public function payInvoice(Rent $rent, Invoice $invoice) {
        $postData = $this->getPostData(request());
        if ($invoice->invoiceable_id == $rent->id) {
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
}
