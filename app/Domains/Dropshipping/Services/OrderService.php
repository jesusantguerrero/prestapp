<?php

namespace App\Domains\Dropshipping\Services;

use App\Domains\Dropshipping\Data\OrderData;
use App\Domains\Dropshipping\Models\Order;

class OrderService
{
    public function create(OrderData $order) {
      return Order::create($order->toArray());
    }

    public function send(Order $order) {
      $order->state()->send();
    }

    public function markAsReceived(Order $order) {
      $order->state()->markAsReceived();
    }

    public function cancel(Order $order) {
      $order->state()->cancel();
    }

    public function return(Order $order) {
      $order->state()->return();
    }
}
