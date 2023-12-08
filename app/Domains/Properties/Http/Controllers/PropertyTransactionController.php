<?php

namespace App\Domains\Properties\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\PropertyTransactionService;

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

    public function saveExpense(Property $property, int $invoiceId = null) {
      try {
        $invoice = PropertyTransactionService::createOrUpdateExpense($property, request()->post(), $invoiceId);
        return response()->json($invoice);
      } catch (Exception $e) {
        return back()->withErrors($e->getMessage());
      }
    }
}
