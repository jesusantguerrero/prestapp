<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Enums\ClientStatus;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Models\Team;
use App\Models\User;
use App\Notifications\ExpiringRentNotice;
use Exception;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Models\Invoice\InvoiceLineTax;

class RentService {
    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $rentData['unit_id'] = $rentData['unit_id'] ?? $rentData['unit']['id'];
      $unit = PropertyUnit::find($rentData['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception('This unit is not available at the time');
      }

      if ($rentData['commission_type'] == Rent::COMMISSION_PERCENTAGE && $rentData['commission'] > 100) {
        throw new Exception(__("The percentage can't be greater than 100%"));
      }

      return DB::transaction(function() use ($rentData, $unit) {
        $property = $unit->property;
        if (!$property->account_id) {
          $property->initAccounts();
          $property = $unit->property()->first();
        }
        $rentData = array_merge($rentData, [
          'account_id' => $property->account_id,
          'owner_id' => $property->owner_id,
          'commission_account_id' => $property->commission_account_id,
          'late_fee_account_id' => $property->late_fee_account_id,
        ]);
        $rent = Rent::create($rentData);
        $rent->unit->update(['status' => PropertyUnit::STATUS_RENTED]);
        $rent->client->update(['status' => ClientStatus::Active]);
        $rent->owner->checkStatus();
        PropertyTransactionService::createDepositTransaction($rent->fresh(), $rentData);
        PropertyTransactionService::generateFirstInvoice($rent);
        RentTransactionService::generateUpToDate($rent->fresh(), isset($rentData['paid_until']), $rentData['paid_until'] ?? null);
      });
    }

    public static function updateRent(Rent $rent, mixed $rentData) {
      return DB::transaction(function() use ($rentData, $rent) {
        $rentData['unit_id'] = $rentData['unit_id'] ?? $rentData['unit']['id'] ?? $rent->unit_id;

        if ($rent->unit_id !== $rentData['unit_id']) {
          $unit = PropertyUnit::find($rentData['unit_id']);
          if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
            throw new Exception('This unit is not available at the time');
          }

          $rent->unit->update(['status' => PropertyUnit::STATUS_AVAILABLE]);
          $unit->update(['status' => PropertyUnit::STATUS_RENTED]);
          $property = $unit->property;
        } else {
          $property = $rent->unit->property;
        }

        if (isset($rentData['commission_type']) && $rentData['commission_type'] == Rent::COMMISSION_PERCENTAGE && $rentData['commission'] > 100) {
          throw new Exception(__("The percentage can't be greater than 100%"));
        }


        if (!$property->account_id) {
          $property->initAccounts();
          $property = $unit->property()->first();
        }


        $rent->update(collect($rentData)->only(['amount', 'notes'])->all());
        $rent->unit->update(['status' => PropertyUnit::STATUS_RENTED]);
        $rent->client->update(['status' => ClientStatus::Active]);
        $rent->owner->checkStatus();

        if (!$rent->payments()->count()) {
          // Invoice::destroy($rent->invoices()->pluck('id'));
          // PropertyTransactionService::createDepositTransaction($rent->fresh(), $rentData);
          // PropertyTransactionService::generateFirstInvoice($rent);
          // RentTransactionService::generateUpToDate($rent->fresh(), isset($rentData['paid_until']), $rentData['paid_until'] ?? null);
        }
      });
    }

    public static function removeRent(Rent $rent) {
      if ($rent->payments()->count()) {
        throw new Exception(__("This rent has payments and can't be eliminated"));
      }

      return DB::transaction(function() use ($rent) {
        if ($rent->isActive()) {
          $rent->unit->update(['status' => PropertyUnit::STATUS_AVAILABLE]);
          if ($rent->client->isActive()) {
            $rent->client->update(['status' => ClientStatus::Active]);
          }
          $rent->owner->checkStatus();
        }
        Invoice::destroy($rent->invoices()->unpaid()->pluck('id'));
        Rent::destroy($rent->id);
      });
    }

    public static function allowedUpdate(mixed $rentData) {
      $validData = [];
      $cantUpdate = collect([
        'rent_id',
        'property_id'
      ]);

      foreach ($rentData as $key => $value) {
        if (!$cantUpdate->contains($key)) {
          $validData[$key] = $value;
        }
      }
      return $validData;
    }

    public static function endTerm(Rent $rent, $formData) {
      if ($rent->status !== Rent::STATUS_CANCELLED) {
        DB::transaction(function () use ($rent, $formData) {
          $rent->update(array_merge(
            $formData,
            ["status" => Rent::STATUS_CANCELLED
          ]));
          $rent->unit->update(['status' => Property::STATUS_AVAILABLE]);
        });
        return $rent;
      }
      throw new Exception('Rent is already cancelled');
    }

    public static function extend(Rent $rent, $formData) {
      if ($rent->status == Rent::STATUS_ACTIVE || $rent->status == Rent::STATUS_EXPIRED) {
        $isExpired = now()->format('Y-m-d') > $rent->end_date;
        $rent->update(array_merge($formData, [
          'status' => Rent::STATUS_ACTIVE,
          'generated_invoice_dates' => $rent->rentInvoices()->select(['id', 'due_date'])->pluck('due_date')->all(),
        ]));
        if ($isExpired) {
          RentTransactionService::generateUpToDate($rent, isset($formData['paid_until']), $formData['paid_until'] ?? null);
        }
        return;
      }
      throw new Exception('Rent is cant be extended/ ');
    }

    public static function getForEdit(int $id) {
      return Rent::where('id', $id)
      ->with([
        'client',
        'invoices',
        'transaction',
        'property',
        'unit'
      ])->first();
    }

    public static function expiredRents($teamId = null, $state = null) {
      $stateRaw = 'CASE
      WHEN DATEDIFF(end_date, now()) < 0 THEN "expired"
      WHEN DATEDIFF(end_date, now()) > 0 AND DATEDIFF( end_date, now()) <= 31 THEN "within_month"
      WHEN DATEDIFF(end_date, now()) > 31 AND DATEDIFF( end_date, now()) <= 60 THEN "next_month"
      END';
      return Rent::selectRaw("DATEDIFF(end_date, now()) AS diff_days,
        $stateRaw
         as expire_in,
         date,
         end_date,
         client_name,
         address,
         id,
         owner_id,
         client_id,
         team_id,
         property_id,
         generated_invoice_dates,
         owner_name,
         user_id
      ")
      ->whereNotNull('end_date')
      ->whereRaw('DATEDIFF(end_date, now()) <= 60')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED])
      ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
      ->when($state, fn ($q) => $q->whereRaw("$stateRaw = '$state'"))
      ->get();
    }

    public static function checkExpiringRents($teamId = null) {
      $expiringRents = Rent::expiredRents($teamId)->get();

      $expiringRents = $expiringRents->groupBy('expire_in');


      foreach ($expiringRents as $state => $rentGroup) {
        $states = [
          "expired" => 'expired',
          'within_month' => '30',
          'next_month' => '60'
        ];

        User::find($rentGroup->first()->user_id)->notify(new ExpiringRentNotice([
          'key' => $state,
          'link' => '/rents?filter[end_date]=<'.now()->addDays(60)->format('Y-m-d'),
          'type' => 'rent',
          "rents" => $rentGroup,
          'date' => now()->format('Y-m-d'),
          'message' => $state == 'expired'
          ? __('You have :count contracts :state', ['count' => $rentGroup->count(), 'state' => __($state)])
          : __('You have :count contracts close to expire within :days', ['count' => $rentGroup->count(), 'days' => $states[$state]])
        ]));
        $count = count($rentGroup);
        activity()
        ->causedBy(Team::find($rentGroup->first()->team_id))
        ->withProperties([
          "rents" => $rentGroup,
        ])
        ->log("System notified about {$count} in $state status");

        echo "System notified about {$count} in $state status";
      }
    }

    public static function updateExpiredRents($teamId = null) {
      $expiredRents = RentService::expiredRents($teamId, 'expired');

      foreach ($expiredRents as $expiredRent) {
          $expiredRent->update([
            'status' => Rent::STATUS_EXPIRED,
            'next_invoice_date' => null,
          ]);

          activity()
          ->causedBy($expiredRent)
          ->log("System changed status of {$expiredRent->client_name} because expired on {$expiredRent->end_date}");

      }
    }

    // stats
    public function listWithInvoicesToGenerate($teamId) {
      return Rent::whereNot('status', Rent::STATUS_CANCELLED)
        ->where('team_id', $teamId)
        ->whereRaw('(next_invoice_date < curdate() or next_invoice_date is null)')
        ->with(['client', 'property', 'unit']);
    }

    //  payments / invoices
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
          invoices.concept concept'
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
        ->groupBy(['clients.names', 'clients.id', 'invoices.debt', 'invoices.due_date', 'invoices.id', 'invoices.concept'])
        ->take(5);

        return $query;
    }

    public static function nextInvoices($teamId, $status = 'unpaid') {
      return self::invoices($teamId, [$status])->get();
    }

    public static function generateNextInvoice($rent) {
      if ($rent->end_date) {
        RentTransactionService::generateUpToDate($rent);
      } else {
        PropertyTransactionService::createInvoice([
          'date' => $rent->next_invoice_date
        ], $rent);
        $rent->update([
          'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
          'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, [$rent->next_invoice_date])
        ]);
      }
    }

    public static function payInvoice(Rent $rent, Invoice $invoice, mixed $postData) {
        if ($invoice->invoiceable_id != $rent->id || $invoice->invoiceable_type !== Rent::class) {
          throw new Exception("This invoice doesn't belongs to this rent");
        }

        if ($invoice && $invoice->debt <= 0) {
            throw new Exception("This invoice is already paid");
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
