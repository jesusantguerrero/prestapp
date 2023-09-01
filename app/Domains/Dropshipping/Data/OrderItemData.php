<?php

namespace App\Domains\Dropshipping\Data;

use App\Domains\Dropshipping\Enums\OrderStatusEnum;
use Spatie\LaravelData\Data;

class OrderItemData extends Data {

 public function __construct(
    public ?int $id,
    public int $user_id,
    public int $team_id,
    public int $order_id,
    public ?int $product_id,
    public ?string $product_image,
    public string $product_name,
    public int $quantity,
    public int $rate,
    public int $customer_id,
    public int $customer_name,
    public OrderStatusEnum $status = OrderStatusEnum::Draft,
  ) {}
}
