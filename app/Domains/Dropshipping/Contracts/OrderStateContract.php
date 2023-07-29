<?php

namespace App\Domains\Dropshipping\Contracts;

interface OrderStateContract  {
  // income
  function send();
  function markAsReceived();
  function cancel();
  function return();
}
