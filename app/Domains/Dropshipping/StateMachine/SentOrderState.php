<?php

namespace App\Domains\Dropshipping\StateMachine;

use App\Domains\Dropshipping\Enums\OrderStatusEnum;

final class SentOrderState extends BaseOrderState  {
  // income
  function markAsReceived() {
    $this->order->update([
      "status" => OrderStatusEnum::Received
    ]);
  }

  function cancel() {
    $this->order->update([
      "status" => OrderStatusEnum::Cancelled
    ]);
  }
}
