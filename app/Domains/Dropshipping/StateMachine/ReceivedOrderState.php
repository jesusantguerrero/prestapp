<?php

namespace App\Domains\Dropshipping\StateMachine;

use App\Domains\Dropshipping\Enums\OrderStatusEnum;

final class ReceivedOrderState extends BaseOrderState  {
  // income
  function return() {
    $this->order->update([
      "status" => OrderStatusEnum::Returned
    ]);
  }
}
