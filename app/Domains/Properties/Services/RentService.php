<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Exception;
use Insane\Journal\Models\Invoice\Invoice;

class RentService {
    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $rentData['unit_id'] = $rentData['unit_id'] ?? $rentData['unit']['id'];
      $unit = PropertyUnit::find($rentData['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception('This unit is not available at the time');
      } else {
        $rentData =
        array_merge($rentData, [
          'account_id' => $unit->property->account_id,
          'owner_id' => $unit->property->owner_id,
          'commission_account_id' => $unit->property->commission_account_id,
          'late_fee_account_id' => $unit->property->late_fee_account_id,
        ]);
        $rent = Rent::create($rentData);
        $rent->unit->update(['status' => PropertyUnit::STATUS_RENTED]);
        PropertyTransactionService::createDepositTransaction($rent, $rentData);
        return PropertyTransactionService::generateFirstInvoice($rent);
      }
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
        $rent->update(array_merge(
          $formData,
          ["status" => Rent::STATUS_CANCELLED
        ]));
        $rent->unit->update(['status' => Property::STATUS_AVAILABLE]);
        return;
      }
      throw new Exception('Rent is already cancelled');
    }

    //  payments / invoices
    public static function invoices($teamId, $statuses = []) {
      $query = Invoice::selectRaw('clients.names contact, clients.id contact_id, invoices.debt, invoices.due_date, invoices.id id, invoices.concept')
        ->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => 'INVOICE',
          'invoiceable_type' => Rent::class
        ]);

        if (count($statuses)) {
          $query->whereIn('invoices.status', $statuses);
        }

        $query
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->groupBy(['clients.names', 'clients.id', 'invoices.debt', 'invoices.due_date', 'invoices.id', 'invoices.concept'])
        ->take(5);

        return $query;
    }

    public static function nextInvoices($teamId, $status = 'unpaid') {
      return self::invoices($teamId, [$status])->get();
    }

    public static function generateNextInvoice($rent) {
      PropertyTransactionService::createInvoice([
        'date' => $rent->next_invoice_date
      ], $rent);
        $rent->update([
          'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
          'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, [$rent->next_invoice_date])
        ]);
    }
}
