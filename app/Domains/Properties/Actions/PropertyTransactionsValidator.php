<?php

namespace App\Domains\Properties\Actions;

use App\Domains\CRM\Models\Client;
use Exception;

class PropertyTransactionsValidator {
    public function canRefund(Client $client, $attemptRefundAmount) {
       if ($client->depositBalance() < $attemptRefundAmount) {
        echo $attemptRefundAmount . " of " . $client->depositBalance() . PHP_EOL;
        throw new Exception(__("The refund is more than the deposit balance"));
       }
    }
}
