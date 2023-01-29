<?php

namespace App\Http\Controllers\CRM;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Services\PropertyTransactionService;

trait OwnerTrait
{
  public function generateOwnerDistribution(Client $client, int $invoiceId = null) {
    PropertyTransactionService::createOwnerDistribution($client, $invoiceId);
    return redirect("/bills");
  }
}
