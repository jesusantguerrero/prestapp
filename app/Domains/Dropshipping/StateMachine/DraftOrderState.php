<?php

namespace App\Domains\Dropshipping\StateMachine;

use App\Domains\Dropshipping\Enums\OrderStatusEnum;

final class DraftOrderState extends BaseOrderState  {
  // income
  function send() {
    $this->order->update([
      "status" => OrderStatusEnum::Sent
    ]);
  }

  function cancel() {
    $this->order->update([
      "status" => OrderStatusEnum::Cancelled
    ]);
  }
}
