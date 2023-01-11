<?php

namespace App\Http\Controllers\Properties;

use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;
use App\Http\Controllers\Controller;

class PropertyTransactionController extends Controller
{
    public function __invoke($transactionType = "DepositRefund") {
      $categories = [
        "deposit-refund" => [
          "page" => "DepositRefund",
          "category" => "security_deposits"
        ]
      ];

      ["page" => $page, "category" => $category] = $categories[$transactionType];
      return inertia("Properties/Transactions/$page", [
        "category" => $category
      ]);
    }

    public function store(Rent $rent, $transactionType) {
      $postData = request()->post();
      $invoice = PropertyTransactionService::createDepositRefund($rent, $postData);
      return response()->json($invoice);
    }
}
