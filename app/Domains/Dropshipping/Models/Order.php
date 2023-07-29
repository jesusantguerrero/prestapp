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

class Order extends Model
{
    use HasFactory;

    protected $attributes = [
      "status" => OrderStatusEnum::Draft
    ];

    protected $casts = [
      'status' => OrderStatusEnum::class
    ];

    public function state(): OrderStateContract {
      return match($this->status) {
        OrderStatusEnum::Draft => new DraftOrderState($this),
        OrderStatusEnum::Sent => new SentOrderState($this),
        OrderStatusEnum::Received => new ReceivedOrderState($this),
        OrderStatusEnum::Cancelled => new CancelledOrderState($this),
        OrderStatusEnum::Returned => new ReturnedOrderState($this),
        default => throw new Exception("This status is not supported")
      };
    }
}
