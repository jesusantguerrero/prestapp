<?php

namespace App\Domains\Dropshipping\Enums;

enum OrderStatusEnum: string {
  // income
  case Draft = 'draft';
  case Sent = 'sent';
  case Received = 'received';
  case Cancelled = 'cancelled';
  case Returned = 'returned';
}
