<?php

namespace App\Domains\Dropshipping\Http\Controllers;

use App\Domains\Dropshipping\Models\Order;
use App\Domains\Dropshipping\Services\InvoiceService;
use App\Http\Controllers\InertiaController;
use Exception;
use Illuminate\Http\Request;
use Insane\Journal\Models\Invoice\Invoice;

class InvoiceController extends InertiaController
{
    public function __construct(Invoice $invoice, private InvoiceService $invoiceService)
    {
        $this->model = $invoice;
        $this->searchable = ['vendor_name', 'amount', 'total'];
        $this->templates = [
            "index" => 'Orders/Index',
            "create" => 'Orders/OrderForm',
            "edit" => 'Orders/OrderForm',
            "show" => 'Orders/Show'
        ];
        $this->validationRules = [];
        $this->sorts = ['created_at'];
        $this->includes = [];
        $this->filters = [];
        $this->resourceName= "orders";
    }


    protected function createResource(Request $request, $postData)
    {
      $this->invoiceService->create($postData, request()->user());
    }


    protected function getEditProps(Request $request, $rent)
    {
      return [
        'orders' => $this->invoiceService->getOrderById($rent->id)
      ];
    }

    public function action(Order $order, string $action) {
      try {
        match($action) {
          "send" => $this->invoiceService->send($order),
          "mark-as-received" => $this->invoiceService->markAsReceived($order),
          "cancel" => $this->invoiceService->cancel($order),
          "return" => $this->invoiceService->return($order),
          default => throw new Exception('this action is nor supported')
        };
      } catch (Exception $e) {
        return redirect()->back()->withErrors($e->getMessage());
      }
    }
}
