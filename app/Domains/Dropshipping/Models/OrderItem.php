<?php

namespace App\Domains\Dropshipping\Models;

use App\Domains\Dropshipping\Contracts\OrderStateContract;
use App\Domains\Dropshipping\Enums\OrderStatusEnum;
use App\Domains\Dropshipping\StateMachine\CancelledOrderState;
use App\Domains\Dropshipping\StateMachine\DraftOrderState;
use App\Domains\Dropshipping\StateMachine\ReceivedOrderState;
use App\Domains\Dropshipping\StateMachine\ReturnedOrderState;
use App\Domains\Dropshipping\StateMachine\SentOrderState;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = "dropshipping_order_items";

    protected $fillable = [
      "order_id", "product_id", "quantity", "price", "total"
    ];

    protected $attributes = [
      "status" => OrderStatusEnum::Draft
    ];

    protected $casts = [
      'status' => OrderStatusEnum::class
    ];
}
