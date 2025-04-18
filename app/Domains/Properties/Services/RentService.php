<?php

namespace App\Domains\Properties\Services;

use Exception;
use App\Models\Team;
use App\Models\User;
use App\Domains\CRM\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Domains\CRM\Enums\ClientStatus;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use App\Notifications\ExpiringRentNotice;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Services\InvoiceService;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Models\Property;
use Insane\Journal\Models\Invoice\InvoiceLineTax;
use \Insane\Journal\Services\InvoiceValidatorService;
use App\Domains\Properties\Services\PropertyUnitService;

class RentService {
    public static function createRent(mixed $rentData, mixed $schedule = null, $user = null) {
      $rentData['unit_id'] = $rentData['unit_id'] ?? $rentData['unit']['id'];
      $unit = PropertyUnit::find($rentData['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception('This unit is not available at the time');
      }

      $existingRent = Rent::where([
        'unit_id' => $rentData['unit_id'],
        'status' => Rent::STATUS_ACTIVE,
      ])->first();

      if ($existingRent) {
        throw new Exception('This unit is already rented, you must cancel the existing rent first');
      }

      if ($rentData['commission_type'] == Rent::COMMISSION_PERCENTAGE && $rentData['commission'] > 100) {
        throw new Exception(__("The percentage can't be greater than 100%"));
      }

      return DB::transaction(function() use ($rentData, $unit, $user) {
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
        PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_RENTED, $user);
        $rent->client->update(['status' => ClientStatus::Active]);
        $rent->owner->checkStatus();
        PropertyTransactionService::createDepositTransaction($rent->fresh(), $rentData);
        PropertyTransactionService::generateFirstInvoice($rent);
        RentTransactionService::generateUpToDate($rent->fresh(), isset($rentData['paid_until']), $rentData['paid_until'] ?? null);
        return $rent;
      });
    }

    public static function updateRent(Rent $rent, mixed $rentData, $user = null) {
      if (!$rent->isActive()) {
        throw new Exception(__("Cant update a cancelled contract"));
      }

      return DB::transaction(function() use ($rentData, $rent, $user) {
        $rentData['unit_id'] = $rentData['unit_id'] ?? $rentData['unit']['id'] ?? $rent->unit_id;

        if ($rent->unit_id !== $rentData['unit_id'] && $rent->isActive()) {
          $unit = PropertyUnit::find($rentData['unit_id']);
          if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
            throw new Exception('This unit is not available at the time');
          }

          PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_AVAILABLE, $user);
          PropertyUnitService::updateStatus($unit, PropertyUnit::STATUS_RENTED, $user);
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

        dd($rentData);

        $shouldUpdateAmount = isset($rentData["amount"]) &&  $rentData["amount"] !== $rent->amount;
        $rent->update(collect($rentData)->only(['amount', 'notes', 'next_invoice_date'])->all());
        PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_RENTED, $user);
        $rent->client->update(['status' => ClientStatus::Active]);
        $rent->owner->checkStatus();

        if ($shouldUpdateAmount) {
          self::updateRentInvoices($rent);
        }
        return $rent;
      });
    }

    public static function removeRent(Rent $rent, $user = null) {
      if ($rent->payments()->count()) {
        throw new Exception(__("This rent has payments and can't be eliminated"));
      }

      return DB::transaction(function() use ($rent, $user) {
        if ($rent->isActive()) {
          PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_AVAILABLE, $user);
          if ($rent->client->isActive()) {
            $rent->client->update(['status' => ClientStatus::Active]);
          }
          $rent->owner->checkStatus();
        }
        Invoice::destroy($rent->invoices()->unpaid()->pluck('id'));
        $rent->update([
          'status' => Rent::STATUS_CANCELLED,
          "end_date" => date('Y-m-d'),
          "cancelled_at" => date('Y-m-d'),
          'next_invoice_date' => null,
        ]);
      });
    }

    public static function allowedUpdate(mixed $rentData) {
      $validData = [];
      $cantUpdate = collect([
        'rent_id',
        'property_id',
      ]);

      foreach ($rentData as $key => $value) {
        if (!$cantUpdate->contains($key)) {
          $validData[$key] = $value;
        }
      }
      return $validData;
    }

    public static function endTerm(Rent $rent, $formData, $user = null) {
      if ($rent->status !== Rent::STATUS_CANCELLED) {
        DB::transaction(function () use ($rent, $formData, $user) {
          $rent->update(array_merge(
            $formData,
            [
              "status" => Rent::STATUS_CANCELLED,
              "end_date" => $formData['move_out_at'] ?? now()->format('Y-m-d')
            ]));
          PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_AVAILABLE, $user);
          Invoice::destroy($rent->invoices()->unpaid()->pluck('id'));
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
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_EXPIRED])
      ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
      ->when($state, fn ($q) => $q->whereRaw("$stateRaw = '$state'"))
      ->get();
    }

    public static function rentsStats($teamId = null) {
      return Rent::where('team_id', $teamId)->active()->count();
    }


    public static function expiredRentStats($teamId = null) {
      $startOfMonth = now()->startOfMonth()->format('Y-m-d');
      $endOfThisMonth = now()->endOfMonth()->format('Y-m-d');
      $lastMonth = now()->addRealMonths(3)->endOfMonth()->format('Y-m-d');

      return Rent::selectRaw("
        sum(CASE WHEN DATEDIFF(end_date, now()) < 0 THEN 1 ELSE 0 END) as expired,
        sum(CASE WHEN end_date >= ? AND end_date <= ? THEN 1 ELSE 0 END) as in_month,
        SUM(CASE WHEN end_date > ? AND end_date <= ? THEN 1 ELSE 0 END) as within_three_months
      ", [$startOfMonth, $endOfThisMonth, $endOfThisMonth, $lastMonth])
      ->whereNotNull('end_date')
      ->whereRaw('end_date <= ?', [$lastMonth])
      ->whereNotIn('status', [Rent::STATUS_CANCELLED])
      ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
      ->first();
    }

    public static function checkExpiringRents($teamId = null) {
      $expiringRents = RentService::expiredRents($teamId)->get();

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

    public static function generateNextInvoice($rent) {
      if ($rent->end_date) {
        RentTransactionService::generateUpToDate($rent);
      } else {
        PropertyTransactionService::createInvoice([
          'date' => $rent->next_invoice_date,
        ], $rent);
        $rent->update([
          'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
          'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, [$rent->next_invoice_date])
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

    public static function create($data, $user = null) {
      $unit = PropertyUnit::find($data['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new \Exception('Unit is not available');
      }

      $rent = Rent::create($data);
      PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_RENTED, $user);
      return $rent;
    }

    public static function transfer($rent, $newUnitId, $user = null) {
      $unit = PropertyUnit::find($newUnitId);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new \Exception('Unit is not available');
      }

      PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_AVAILABLE, $user);
      PropertyUnitService::updateStatus($unit, PropertyUnit::STATUS_RENTED, $user);
      $rent->update(['unit_id' => $newUnitId]);
      return $rent;
    }

    public static function cancel($rent, $user = null) {
      $rent->update(['status' => Rent::STATUS_CANCELLED]);
      PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_AVAILABLE, $user);
      return $rent;
    }

    public static function renew($rent, $data, $user = null) {
      $rent->update($data);
      PropertyUnitService::updateStatus($rent->unit, PropertyUnit::STATUS_RENTED, $user);
      return $rent;
    }

    public static function occupancy($teamId, $clientId) {
      $properties = Property::where([
        'team_id' => $teamId,
        'owner_id' => $clientId
      ])->select('id', 'name', 'address')->get();

      $rents = Rent::where([
        'team_id' => $teamId,
        'unit_id' => $properties->pluck('id')
      ])->whereIn('property_id', $properties->pluck('id'))->get();
      
      $totalUnits = PropertyUnit::where([
        'team_id' => $teamId, 
      ])
      ->whereIn('property_id', $properties->pluck('id'))
      ->count();
      
      $occupiedUnits = $rents->count();
      $rate = $occupiedUnits / $totalUnits * 100;

      

      $properties->each(function ($property) use ($rents) {
        $property->paid = $rents->where('status', Rent::STATUS_ACTIVE)->count();
        $property->unpaid = $rents->where('status', Rent::STATUS_LATE)->count();
        $property->late = $rents->where('status', Rent::STATUS_LATE)->count();
        $property->expired = $rents->where('status', Rent::STATUS_EXPIRED)->count();
        $property->cancelled = $rents->where('status', Rent::STATUS_CANCELLED)->count();
      });

      return [
        'occupied' => $occupiedUnits,
        'total' => $totalUnits,
        'rate' => $rate,
        'properties' => $properties
      ];
    }
}
