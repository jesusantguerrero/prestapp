<?php

namespace App\Http\Controllers\Properties;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
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
      ]);
    }

    protected function createResource(Request $request, $postData)
    {
        return RentService::createRent($postData);
    }

    protected function updateResource($resource, $postData)
    {
        $resource->update(RentService::allowedUpdate($postData));
        return $resource;
    }

    protected function getEditProps(Request $request, $id)
    {
        return [
            'rents' => Rent::where('id', $id)->with(['client', 'invoices', 'transaction'])->first()
        ];
    }

    // Payments
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

    public function generateNextInvoice(Rent $rent) {
      RentService::generateNextInvoice($rent);
    }
}
