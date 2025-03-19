<?php

namespace App\Services;

use App\Domains\Properties\Models\PropertyUnit;
use App\Models\PropertyUnitSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PropertyUnitSnapshotService
{
    /**
     * Create end-of-month snapshots for all units
     */
    public function createMonthEndSnapshots(Carbon $date): Collection
    {
        $endOfMonth = $date->copy()->endOfMonth();
        
        return PropertyUnit::with(['property', 'currentRent', 'currentRent.tenant'])
            ->get()
            ->map(function (PropertyUnit $unit) use ($endOfMonth) {
                return PropertyUnitSnapshot::create([
                    'property_unit_id' => $unit->id,
                    'property_id' => $unit->property_id,
                    'snapshot_date' => $endOfMonth,
                    'status' => $unit->status,
                    'rent_status' => $unit->currentRent?->status,
                    'tenant_id' => $unit->currentRent?->tenant_id,
                    'monthly_rent' => $unit->currentRent?->amount,
                    'invoice_status' => $this->determineInvoiceStatus($unit),
                    'invoice_amount' => $unit->currentRent?->amount,
                ]);
            });
    }

    /**
     * Get snapshots for a specific month
     */
    public function getMonthSnapshots(Carbon $date): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return PropertyUnitSnapshot::with(['propertyUnit', 'property', 'tenant'])
            ->whereBetween('snapshot_date', [$startOfMonth, $endOfMonth])
            ->get();
    }

    /**
     * Get historical snapshots for a specific unit
     */
    public function getUnitHistory(PropertyUnit $unit, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = PropertyUnitSnapshot::with(['property', 'tenant'])
            ->where('property_unit_id', $unit->id);

        if ($startDate) {
            $query->where('snapshot_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('snapshot_date', '<=', $endDate);
        }

        return $query->orderBy('snapshot_date', 'desc')->get();
    }

    /**
     * Determine invoice status based on unit's current state
     */
    protected function determineInvoiceStatus(PropertyUnit $unit): ?string
    {
        if (!$unit->currentRent) {
            return null;
        }

        // Logic to determine invoice status based on your business rules
        // This is just an example - adjust according to your needs
        $latestInvoice = $unit->currentRent->invoices()->latest()->first();
        
        if (!$latestInvoice) {
            return 'pending';
        }

        return $latestInvoice->status === 'paid' ? 'paid' : 'overdue';
    }
} 