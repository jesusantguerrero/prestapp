<?php

namespace App\Domains\Dropshipping\Data;

use App\Domains\Dropshipping\Enums\OrderStatusEnum;
use Spatie\LaravelData\Data;

class OrderData extends Data {

 public function __construct(
    public ?int $id,
    public int $user_id,
    public int $team_id,
    public string $date,
    public OrderStatusEnum $status = OrderStatusEnum::Draft,
    public ?string $sent_at,
    public ?string $received_at,
    public ?string $cancelled_at,
  ) {}
}
