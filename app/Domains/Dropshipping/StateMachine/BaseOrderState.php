<?php

namespace App\Domains\Dropshipping\StateMachine;

use App\Domains\Dropshipping\Contracts\OrderStateContract;
use App\Domains\Dropshipping\Models\Order;
use Exception;

class BaseOrderState implements OrderStateContract  {
  public function __construct(public Order $order)
  {

  }
  // income
  function send() {
    throw new Exception("Action cant be performed in this state");
  }
  function markAsReceived() {
    throw new Exception("Action cant be performed in this state");
  }
  function cancel() {
    throw new Exception("Action cant be performed in this state");
  }
  function return() {
    throw new Exception("Action cant be performed in this state");
  }
}
