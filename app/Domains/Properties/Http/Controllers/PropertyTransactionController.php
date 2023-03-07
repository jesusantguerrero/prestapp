<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;
use App\Http\Controllers\Controller;
use Exception;

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
      return $this->$transactionType($rent, $postData);
    }

    public function expense($rent, $postData, int $invoiceId = null) {
      $invoice = PropertyTransactionService::createOrUpdateExpense($rent, $postData, $invoiceId);
      return response()->json($invoice);
    }

    public function createDepositRefund(Rent $rent) {
      return inertia("Properties/Transactions/DepositRefund", [
        "category" => 'security_deposits',
        "client" => $rent->client,
        "refunds" => $rent->refunds
      ]);
    }

    public function refund($rent, $postData) {
      try {
        PropertyTransactionService::createDepositRefund($rent, $postData);
        return back();
      } catch (Exception $e) {
        back()->withErrors(["error" => $e->getMessage()]);
      }
    }
}
