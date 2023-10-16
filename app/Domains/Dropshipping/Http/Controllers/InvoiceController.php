<?php

namespace App\Domains\Dropshipping\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Domains\Dropshipping\Models\Order;
use Insane\Journal\Models\Invoice\Invoice;
use App\Http\Controllers\InertiaController;
use App\Domains\Dropshipping\Services\InvoiceService;

class InvoiceController extends InertiaController
{
    public function __construct(Invoice $invoice, private InvoiceService $invoiceService)
    {
        $this->model = $invoice;
        $this->searchable = ['concept', 'amount', 'total'];
        $this->templates = [
            "index" => 'Orders/Index',
            "create" => 'Orders/OrderForm',
            "edit" => 'Orders/OrderForm',
            "show" => 'Orders/Show'
        ];
        $this->validationRules = [];
        $this->sorts = ['-date', '-id'];
        $this->includes = ['client'];
        $this->filters = [];
        $this->resourceName= "orders";
    }


    protected function createResource(Request $request, $postData)
    {
      $this->invoiceService->create($postData, request()->user());
    }

    public function edit(Request $request, int $id) {
      return inertia($this->templates['edit'],[
        "invoices" => $this->invoiceService->getOrderById($id)
      ]);
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
