<?php

namespace App\Domains\Properties\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Services\PropertyTransactionService;

class RentTransactionController extends Controller
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
      try {
        return $this->$transactionType($rent, $postData);
      } catch (Exception $e) {
        return back()->withErrors($e->getMessage());
      }
    }

    public function fee($rent, $postData, int $invoiceId = null) {
      $invoice = PropertyTransactionService::createLateFee($rent, $postData);
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

    public function applyDeposit(Rent $rent, Invoice $invoice) {
      $postData = request()->post();
      try {
        PropertyTransactionService::applyDepositTo($rent, $invoice, $postData);
        return back();
      } catch (Exception $e) {
        back()->withErrors(["error" => $e->getMessage()]);
      }
    }
}
