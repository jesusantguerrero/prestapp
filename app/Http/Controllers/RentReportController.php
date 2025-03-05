<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentReportController extends Controller
{
    public function occupancy(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        // Get all property units with their current rent status
        $units = PropertyUnit::query()
            ->with(['property', 'owner', 'currentRent'])
            ->when($request->owner_id, function ($query, $ownerId) {
                return $query->where('owner_id', $ownerId);
            })
            ->when($request->property_id, function ($query, $propertyId) {
                return $query->where('property_id', $propertyId);
            })
            ->where([
                'team_id' => $request->user()->currentTeam->id,
            ])
            ->get()
            ->groupBy('owner.display_name')
            ->map(function ($ownerUnits) {
                return $ownerUnits->groupBy('property_id')
                    ->map(function ($propertyUnits) {
                        $units = $propertyUnits;
                        $totalUnits = $units->count();
                        
                        // Get current rent status for each unit
                        $units = $units->map(function ($unit) {
                            $rent = $unit->currentRent;
                            $unit->rent_status = $rent ? $rent->status : null;
                            $unit->rent_id = $rent ? $rent->id : null;
                            return $unit;
                        });

                        // Calculate rented units (including retained)
                        $rentedUnits = $units->filter(fn($unit) => $unit->rent_id);
                        $rentedCount = $rentedUnits->count();
                        $realRentedCount = $units->filter(fn($unit) => $unit->status === Property::STATUS_RENTED)->count();

                        return [
                            'ownerName' => $units->first()->owner->display_name,
                            'propertyId' => $units->first()->property_id,
                            'propertyName' => $units->first()?->property?->name,
                            'totalUnits' => $totalUnits,
                            
                            // Payment Status
                            'paid' => $units->filter(fn($unit) => $unit->rent_status === 'PAID')->count(),
                            'unpaid' => $units->filter(fn($unit) => in_array($unit->rent_status, ['LATE', 'PARTIALLY_PAID']))->count(),
                            'available' => $units->filter(fn($unit) => !$unit->rent_id)->count(),
                            'rented' => $rentedCount,
                            'real_rented' => $realRentedCount,
                            
                            // Unit Status
                            'building' => $units->filter(fn($unit) => $unit->status === Property::STATUS_BUILDING)->count(),
                            'maintenance' => $units->filter(fn($unit) => $unit->status === Property::STATUS_MAINTENANCE)->count(),
                            
                            // Rent Status
                            'active' => $units->filter(fn($unit) => $unit->rent_status === 'ACTIVE')->count(),
                            'late' => $units->filter(fn($unit) => $unit->rent_status === 'LATE')->count(),
                            'grace' => $units->filter(fn($unit) => $unit->rent_status === 'GRACE')->count(),
                            'cancelled' => $units->filter(fn($unit) => $unit->rent_status === 'CANCELLED')->count(),
                            'expired' => $units->filter(fn($unit) => $unit->rent_status === 'EXPIRED')->count(),
                            
                            // Unit Details
                            'totalPrice' => $units->sum('price'),
                            'totalCommission' => $units->sum('commission'),
                            'averagePrice' => $units->avg('price'),
                            'averageCommission' => $units->avg('commission'),
                            
                            // Unit Types
                            'byBedrooms' => $units->groupBy('bedrooms')
                                ->map(fn($group) => $group->count()),
                            'byBathrooms' => $units->groupBy('bathrooms')
                                ->map(fn($group) => $group->count()),
                            
                            // Amenities
                            'amenities' => $units->flatMap(fn($unit) => json_decode($unit->amenities, true) ?? [])
                                ->countBy(),
                            
                            // Rates
                            'occupancyRate' => ($rentedCount / $totalUnits) * 100,
                            'maintenanceRate' => ($units->filter(fn($unit) => $unit->status === Property::STATUS_MAINTENANCE)->count() / $totalUnits) * 100,
                            'buildingRate' => ($units->filter(fn($unit) => $unit->status === Property::STATUS_BUILDING)->count() / $totalUnits) * 100,
                            'revenueRate' => ($units->sum('price') / $totalUnits) * ($rentedCount / $totalUnits)
                        ];
                    });
            });
        $data = [
            'invoices' => $units,
            'type' => 'occupancy',
            'outstanding' => $units->flatMap(fn($units) => $units->values())->sum('unpaid'),
            'paid' => $units->flatMap(fn($units) => $units->values())->sum('paid'),
            'total' => $units->flatMap(fn($units) => $units->values())->sum('totalUnits'),
            'available' => $units->flatMap(fn($units) => $units->values())->sum('available'),
            'rented' => $units->flatMap(fn($units) => $units->values())->sum('rented'),
            'real_rented' => $units->flatMap(fn($units) => $units->values())->sum('real_rented'),
            'lateDays' => 0, // This should be calculated based on your business logic
            'properties' => Property::select('id', 'name')->get(),
            'owners' => Client::select('id', 'display_name')->get(),
            'businessData' => Setting::getByTeam($request->user()->currentTeam->id),
            'user' => $request->user(),
            'section' => 'occupancy',
            'serverSearchOptions' => [
                'dates' => [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ],
            ],
        ];  

        dd($data);
        return Inertia::render('Rents/Reports/Occupancy', $data);
    }
} 