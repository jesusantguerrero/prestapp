<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Exception;
use Illuminate\Http\Request;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

class RentController extends InertiaController
{
    public function __construct(Rent $rent)
    {
        $this->model = $rent;
        $this->searchable = ['client_name', 'owner_name', 'address'];
        $this->templates = [
            "index" => 'Rents/Index',
            "create" => 'Rents/RentForm',
            "edit" => 'Rents/RentForm',
            "show" => 'Rents/Show'
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'commission' => 'numeric',
            'installments' => 'array'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['client', 'property', 'unit'];
        $this->filters = [];
        $this->page = 1;
        $this->limit = 10;
    }

    public function create(Request $request) {
      $clientId = $request->query('client');
      $unitId = $request->query('unit');

      $unit = PropertyService::hintUnit($unitId);
      $client = PropertyService::hintClient($clientId);

      return inertia($this->templates['create'], [
        'rents' => null,
        'paymentAccount' => Account::findByDisplayId('real_state', request()->user()->current_team_id),
        'property' => $unit?->property,
        'unit' => $unit,
        "client" => $client
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

    protected function getEditProps(Request $request, $rent)
    {
      return [
        'rents' => RentService::getForEdit($rent->id)
      ];
    }

    public function validateDelete(Request $request, $rent) {
      if ($rent->payments()->count()) {
        throw new Exception(__("This rent has payments and can't be eliminated"));
      }

      return true;
    }

    // Payments
    public function payInvoice(Rent $rent, Invoice $invoice) {
        RentService::payInvoice($rent, $invoice, $this->getPostData());
    }

    public function deletePayment(Rent $rent, Invoice $invoice, Payment $payment) {
        RentService::deletePayment($rent, $invoice, $payment);
    }

    public function generateNextInvoice(Rent $rent) {
      RentService::generateNextInvoice($rent);
    }

    // Tabs
    public function getSection(Rent $rent, string $section) {
      $resourceName = $this->resourceName ?? $this->model->getTable();
      $resource = Rent::with([
        'client',
        'payments',
        'payments.payable',
        'rentInvoices',
        'depositInvoices',
        'rentExpenses',
      ])->where('id', $rent->id)
      ->first()
      ->toArray();

      $sectionName = ucfirst($section);

      return inertia("Rents/Tabs/$sectionName",
      [
          $resourceName => array_merge($resource, $this->$section($rent)),
          "currentTab" => $section,
      ]);
    }

    public function payments(Rent $rent) {
      return [
        "payments" => $rent->payments,
      ];
    }

    public function invoices(Rent $rent) {
      return [
        "invoices" => $rent->rentInvoices,
      ];
    }

    public function deposits(Rent $rent) {
      return [
        "deposits" => $rent->depositInvoices,
      ];
    }

    public function expenses(Rent $rent) {
      return [
        "expenses" => $rent->rentExpenses,
      ];
    }

}
