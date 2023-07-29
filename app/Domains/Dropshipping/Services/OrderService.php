<?php

namespace App\Domains\Dropshipping\Models;

class OrderService
{
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
