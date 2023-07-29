<?php

namespace App\Domains\Loans\Http\Controllers;

use App\Domains\Dropshipping\Models\Order;
use App\Domains\Dropshipping\Models\OrderService;
use App\Http\Controllers\InertiaController;
use Exception;

class OrderController extends InertiaController
{
    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->searchable = ['vendor_name', 'amount', 'total'];
        $this->templates = [
            "index" => 'Order/Index',
            "create" => 'Order/EditForm',
            "edit" => 'Order/EditForm',
            "show" => 'Order/Show'
        ];
        $this->validationRules = [
            'vendor_id' => 'numeric',
            'amount' => 'numeric',
            'interest_rate' => 'numeric|max:100',
            'items' => 'array',
            'source_account_id' => 'required|numeric'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['vendor'];
        $this->filters = [];
        $this->resourceName= "orders";
    }

    public function action(Order $order, string $action, OrderService $orderService) {
      match($action) {
        "send" => $orderService->send($order),
        "mark-as-received" => $orderService->markAsReceived($order),
        "cancel" => $orderService->cancel($order),
        "return" => $orderService->return($order),
        default => throw new Exception('this action is nor supported')
      };
    }
}
