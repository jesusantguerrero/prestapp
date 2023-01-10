<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;

class PropertyTransactionController extends Controller
{
    public function __invoke($transactionType = "DepositRefund") {
      $categories = [
        "deposit-refund" => [
          "page" => "DepositRefund",
          "category" => "liabilities"
        ]
      ];

      ["page" => $page, "category" => $category] = $categories[$transactionType];
      return inertia("Properties/Transactions/$page", [
        "category" => $category
      ]);
    }
}
