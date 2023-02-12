<?php

namespace App\Domains\Loans\Services;

use App\Domains\Accounting\DTO\ReceiptData;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Loans\Helpers\LoanValidator;
use App\Domains\Loans\Jobs\CreateLoanTransaction;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Core\PaymentDocument;

class LoanService {
    public static function createLoan(mixed $loanData, mixed $installments) {
        $validator = new LoanValidator($loanData);
        if ($validator->isValid()) {
            try {
                DB::beginTransaction();
                $loan = Loan::create(array_merge($loanData, [
                  'status' => Loan::STATUS_DISPOSED,
                ]));
                LoanService::createInstallments($loan, $installments);
                CreateLoanTransaction::dispatch($loan);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
            DB::commit();
        }
    }

    public static function updateLoan(mixed $loanData, Loan $loan) {
      $loan->update($loanData);
      CreateLoanTransaction::dispatch($loan);
    }

    public static function cancel(Loan $loan, $data, $type = 'cancel') {
      $loan->update([
        'cancel_type' => $type,
        'cancelled_at' => $data['date'],
        'cancel_reason' => $data['reason'],
        'cancel_at_debt' => $data['cancel_at_debt'] ?? 0,
        'status' => Loan::STATUS_CANCELLED,
      ]);
      LoanInstallment::where([
        'loan_id' => $loan->id,
      ])->unpaid()->delete();
    }

    public static function createInstallments(Loan $loan, mixed $installments) {
        LoanInstallment::where([
            'loan_id' => $loan->id,
            'payment_status' => LoanInstallment::STATUS_PENDING
        ])->delete();
        foreach ($installments as $item) {
            $loan->installments()->create([
                "team_id" => $loan->team_id,
                "user_id" => $loan->user_id,
                "client_id" => $loan->client_id,
                "due_date" => $item["due_date"],
                "number" => $item["number"],
                "initial_balance" => $item["initial_balance"],
                "amount" => $item["amount"],
                "interest" => $item["interest"],
                "principal" => $item["principal"],
                "final_balance" => $item["final_balance"],
                "status" => LoanInstallment::STATUS_PENDING,
            ]);
        }

        $loan->save();
        return $loan;
    }

    public static function disposedCapitalFor(int $teamId) {
        return Loan::where('team_id', $teamId)->sum('amount');
    }

    public static function expectedInterestFor(int $teamId) {
        return LoanInstallment::where('team_id', $teamId)->sum('interest');
    }

    public static function paidInterestFor(int $teamId) {
        return LoanInstallment::where('team_id', $teamId)->sum('interest_paid');
    }

    public static function invoices(int $teamId, int $clientId = null) {
        return LoanInstallment::byTeam($teamId);
        // ->byClient($clientId);
    }

    public static function paymentReport(int $teamId, int $clientId = null, $type) {
      if ($type == 'pending' || $type == 'all') {
        $repayments = LoanInstallment::byTeam($teamId);
        return $type == 'all' ? $repayments : $repayments->unpaid();
      } else {
        return Payment::where('team_id', $teamId)
        ->with(['payable'])
        ->byPayable(LoanInstallment::class);
      }
    }

    public static function getStats(Loan $loan) {
      return $loan->installments()->selectRaw("sum(principal - principal_paid) as outstandingPrincipal, sum(interest - interest_paid) as outstandingInterest, sum(late_fee - late_fee_paid) as outstandingFees,sum(amount_due) as outstandingTotal
      ")->first();
    }

    public static function paidInterestByPeriod($teamId, $timeUnit = 'month', $timeUnitDiff = 2 , $type = 'expenses') {
      $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
      $startDate = Carbon::now()->startOfYear()->format('Y-m-d');

      $results = ReportHelper::getTransactionsByAccount($teamId, ['expected_interest_loans'], $startDate, $endDate);
      $results =  collect($results["expected_interest_loans"] ?? []);
      
      return [
        "year" => $results->sum('outcome'),
        "months" => $results->values(),
        "avg" => $results->avg('outcome')
      ];
    }

    public static function getReceipt(Loan $loan, PaymentDocument $document) {
      $documentData = $document->toArray();
      $documentData['resource_name'] = 'Prestamo';
      $documentData['client_name'] = $loan->client->display_name;
      $documentData['total_in_words'] = InvoiceHelper::numberToWords($document->amount);

      $description = $document->payments->reduce(function ($description, $payment) {
        $concept = $payment->payable->amount_due > 0 ? "Abono" : "Pago";

        return $description . "$concept cuota {$payment->payable->number} ";
      });

      $receipt = new ReceiptData(
        Setting::getBySection($loan->team_id, 'business'),
        $loan->client,
        $description,
        $document->payments->map(function ($payment) use ($loan) {
          $concept = $payment->payable->amount_due > 0 ? "Abono" : "Pago";
          return [
            "concept" => "$concept cuota {$payment->payable->number} de {$loan->repayment_count}",
            "amount" => $payment->amount
          ];
        }),
        $document->amount,
        $document->meta_data,
        "**Verifique su recibo valor no reembolsable**"
      );

      return $receipt;
    }

    public static function deletePaymentDocument(Loan $loan, PaymentDocument $document) {
      $document->delete();
    }
}
