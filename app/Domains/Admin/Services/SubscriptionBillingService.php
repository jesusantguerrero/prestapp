<?php

namespace App\Domains\Admin\Services;

use Exception;
use App\Domains\CRM\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Treasurer\Models\Subscription;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Services\InvoiceService;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use Insane\Journal\Models\Invoice\InvoiceLineTax;
use \Insane\Journal\Services\InvoiceValidatorService;

class SubscriptionBillingService {

    // stats
    public function listWithInvoicesToGenerate($teamId) {
      return Rent::whereNot('status', Rent::STATUS_CANCELLED)
        ->where('team_id', $teamId)
        ->whereRaw('(next_invoice_date < curdate() or next_invoice_date is null)')
        ->with(['client', 'property', 'unit']);
    }

    public function getListKpi($teamId) {
      $statuses = [
        Rent::STATUS_ACTIVE,
        Rent::STATUS_CANCELLED,
        Rent::STATUS_EXPIRED,
        Rent::STATUS_GRACE,
        Rent::STATUS_LATE,
      ];


      $stateRaw = [];
      foreach ($statuses as $status) {
        $stateRaw[] = "SUM(CASE WHEN status = '$status' THEN 1 ELSE 0 END) as $status";
      }

      $stateRaw = implode(",", $stateRaw);

      return Rent::where('team_id', $teamId)
      ->selectRaw("
        count(id) as TOTAL,
        $stateRaw
      ")
      ->first();
    }

    //  payments / invoices
    public static function updateRentInvoices(Rent $rent) {
      $invoicesToUpdate = $rent->invoices()->where([
        'invoices.status' => Invoice::STATUS_UNPAID
      ])
      ->get();
      $oldAmount = 0;

      // dd("update amount", $rent, $invoicesToUpdate);
      $invoiceService = new InvoiceService(new InvoiceValidatorService());
      if (count($invoicesToUpdate)) {
        $oldAmount = $invoicesToUpdate->first()->total;
        foreach ($invoicesToUpdate as $invoice) {
          $invoiceService->update($invoice, ["total" => $rent->amount]);
        }

        $author = auth()?->user()?->name ?? "Admin";
        activity()
          ->performedOn($invoice)
          ->withProperties([
            "rent_id" => $rent->id,
            "client_id" => $invoice->client->display_name,
            "oldAmount" => $oldAmount,
            "amount" => $rent->amount,
            "from" => $invoicesToUpdate[0]->date,
            "date" => $invoicesToUpdate->last()->date,
          ])
          ->log("$author updates rent's invoices of rent $rent->id of {$invoice->client->display_name} from {$oldAmount} to {$rent->amount}");
      }

    }

    public static function invoices($teamId, $statuses = []) {
      $query = Invoice::selectRaw('
          clients.names contact,
          clients.names client_name,
          clients.id contact_id,
          invoices.debt,
          invoices.series,
          invoices.number,
          invoices.date,
          invoices.due_date,
          invoices.total,
          invoices.id id,
          invoices.type type,
          invoices.status,
          invoices.invoiceable_id,
          invoices.category_type,
          rents.address category,
          rents.owner_name owner_name,
          invoices.description description,
          invoices.concept concept,
          rents.status rent_status,
          rents.move_out_at'
        )->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => 'INVOICE',
          'invoiceable_type' => Rent::class
        ]);

        if (count($statuses)) {
          $query->whereIn('invoices.status', $statuses);
        }

        $query
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->join('rents', 'rents.id', '=', 'invoices.invoiceable_id')
        ->groupBy(['clients.names', 'clients.id', 'invoices.debt', 'invoices.due_date', 'invoices.id', 'invoices.concept']);

        return $query;
    }

    public static function invoiceByPaymentStatus($teamId, string $startDate = null, string $endDate = null) {
      $startMonth = $startDate ?? now()->startOfMonth()->format('Y-m-d');
      $endMonth = $endDate ??  now()->endOfMonth()->format('Y-m-d');

      return  Invoice::query()
      ->select(DB::raw("count(id) invoicesCount, sum(invoices.total) total, sum(invoices.total-debt) paid, sum(debt) outstanding, sum(
        CASE
        WHEN invoices.debt > 0 THEN 1
        ELSE 0
      END) outstandingInvoices"))
      ->where([
        'invoices.team_id' => $teamId,
        'invoices.type' => 'INVOICE',
        'invoiceable_type' => Rent::class
      ])
      ->whereBetween('due_date', [$startMonth, $endMonth])
      ->groupBy(DB::raw("DATE_FORMAT(due_date, '%Y-%m-01')"))
      ->first();
    }

    public static function commissions($teamId, $statuses = []) {
      $query = InvoiceLineTax::selectRaw('
          clients.names client_name,
          clients.id contact_id,
          (CASE
            WHEN invoices.status = "paid" then 0
            ELSE invoice_line_taxes.amount
          END) as debt,
          invoices.debt invoice_debt,
          invoices.date,
          invoice_line_taxes.concept category,
          invoice_lines.concept account_name,
          invoices.due_date,
          invoice_line_taxes.amount total,
          invoices.id id,
          invoices.series,
          invoices.number,
          invoices.status,
          invoices.concept concept'
        )->where([
          'invoices.team_id' => $teamId,
          'invoiceable_type' => Client::class
        ]);

        if (count($statuses)) {
          $query->whereIn('invoices.status', $statuses);
        }

        $query
        ->join('invoices', 'invoices.id', '=', 'invoice_line_taxes.invoice_id')
        ->join('invoice_lines', 'invoice_lines.id', '=', 'invoice_line_taxes.invoice_line_id')
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->groupBy(['invoices.debt', 'invoices.due_date', 'invoices.id', 'invoices.concept'])
        ->orderBy('date', 'desc')
        ->take(5);

        return $query;
    }

    public static function nextInvoices($teamId, $status = 'unpaid') {
      return self::invoices($teamId, [$status])->get();
    }

    public static function generateNextInvoice(Subscription $subscription) {
      if ($subscription->ends_at) {
        BillingTransactionService::generateUpToDate($subscription);
      } else {
        BillingTransactionService::createInvoice([
          'date' => $subscription->next_billing_date,
        ], $subscription);
        $subscription->update([
          'next_billing_date' => InvoiceHelper::getNextDate($subscription->next_billing_date->format('Y-m-d')),
        ]);
      }
    }

    public static function payInvoice(Rent $rent, Invoice $invoice, mixed $postData) {
        if ($invoice->invoiceable_id != $rent->id || $invoice->invoiceable_type !== Rent::class) {
          throw new Exception(__("This invoice doesn't belongs to this rent"));
        }

        if ($invoice && $invoice->debt <= 0) {
            throw new Exception(__("This invoice is already paid"));
        }

        DB::transaction(function () use ($invoice, $postData, $rent) {
          $invoice->createPayment(array_merge($postData, [
            "client_id" => $rent->client_id,
            "account_id" => $formData['account_id'] ?? Account::findByDisplayId('real_state', $rent->team_id)->id,
            "documents" => [[
                "payable_id" => $invoice->id,
                "payable_type" => Invoice::class,
                "amount" => $postData['amount'] ?? $invoice->debt
            ]]
          ]));

          $invoice->save();
          $rent->client->checkStatus();
        });
    }

    public static function updatePayment(Rent $rent, Invoice $invoice, Payment $payment, mixed $postData) {
        if ($invoice->invoiceable_id != $rent->id || $invoice->invoiceable_type !== Rent::class) {
          throw new Exception("This invoice doesn't belongs to this rent");
        }

        $payment->update($postData);
        $payment->createTransaction();
        $invoice->save();
        $rent->client->checkStatus();
    }

    public static function deletePayment(Rent $rent, Invoice $invoice, Payment $payment) {
        if ($invoice->invoiceable_id != $rent->id || $invoice->invoiceable_type !== Rent::class) {
          throw new Exception("This invoice doesn't belongs to this rent");
        }

        $payment->delete();
        $invoice->save();
        $rent->client->checkStatus();
    }

    public static function deleteInvoicePayments(Rent $rent, Invoice $invoice) {
        if ($invoice->invoiceable_id != $rent->id || $invoice->invoiceable_type !== Rent::class) {
          throw new Exception("This invoice doesn't belongs to this rent");
        }

        Payment::destroy($invoice->payments->pluck('id'));

        $invoice->save();
        $rent->client->checkStatus();

    }
}
