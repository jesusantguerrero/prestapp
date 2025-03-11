<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentReportController extends Controller
{
    public function occupancy(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        // Optimize query with specific field selection and eager loading
        $units = PropertyUnit::query()
            ->select([
                'property_units.*',
                'properties.name as property_name',
                'clients.display_name as owner_name',
                'rents.status as rent_status',
                'rents.id as rent_id'
            ])
            ->join('properties', 'property_units.property_id', '=', 'properties.id')
            ->join('clients', 'property_units.owner_id', '=', 'clients.id')
            ->leftJoin('rents', function($join) {
                $join->on('property_units.id', '=', 'rents.unit_id')
                    ->whereNotIn('rents.status', [Rent::STATUS_CANCELLED, Rent::STATUS_EXPIRED])
                    ->whereNull('rents.move_out_at');
            })
            ->when($request->owner_id, function ($query, $ownerId) {
                return $query->where('property_units.owner_id', $ownerId);
            })
            ->when($request->property_id, function ($query, $propertyId) {
                return $query->where('property_units.property_id', $propertyId);
            })
            ->where('property_units.team_id', $request->user()->currentTeam->id)
            ->get()
            ->sortBy('owner_name')
            ->groupBy('owner_name')
            ->map(function ($ownerUnits) {
                return $ownerUnits->groupBy('property_id')
                    ->map(function ($propertyUnits) {
                        $units = $propertyUnits;
                        $totalUnits = $units->count();
                        
                        // Pre-calculate common filters
                        $rentedUnits = $units->whereNotNull('rent_id');
                        $rentedCount = $rentedUnits->count();
                        
                        // Create status counts using a single loop
                        $statusCounts = $units->reduce(function ($counts, $unit) {
                            $counts['paid'] += $unit->rent_status === 'PAID' ? 1 : 0;
                            $counts['unpaid'] += in_array($unit->rent_status, ['LATE', 'PARTIALLY_PAID']) ? 1 : 0;
                            $counts['building'] += $unit->status === Property::STATUS_BUILDING ? 1 : 0;
                            $counts['maintenance'] += $unit->status === Property::STATUS_MAINTENANCE ? 1 : 0;
                            $counts['active'] += $unit->rent_status === 'ACTIVE' ? 1 : 0;
                            $counts['late'] += $unit->rent_status === 'LATE' ? 1 : 0;
                            $counts['grace'] += $unit->rent_status === 'GRACE' ? 1 : 0;
                            $counts['cancelled'] += $unit->rent_status === 'CANCELLED' ? 1 : 0;
                            $counts['expired'] += $unit->rent_status === 'EXPIRED' ? 1 : 0;
                            $counts['real_rented'] += $unit->status === Property::STATUS_RENTED ? 1 : 0;
                            return $counts;
                        }, [
                            'paid' => 0, 'unpaid' => 0, 'building' => 0, 'maintenance' => 0,
                            'active' => 0, 'late' => 0, 'grace' => 0, 'cancelled' => 0,
                            'expired' => 0, 'real_rented' => 0
                        ]);

                        // Calculate sums in a single loop
                        $financials = $units->reduce(function ($sums, $unit) {
                            $sums['totalPrice'] += $unit->price ?? 0;
                            $sums['totalCommission'] += $unit->commission ?? 0;
                            return $sums;
                        }, ['totalPrice' => 0, 'totalCommission' => 0]);

                        // Calculate distributions efficiently
                        $byBedrooms = $units->groupBy('bedrooms')->map->count();
                        $byBathrooms = $units->groupBy('bathrooms')->map->count();
                        
                        // Optimize amenities counting
                        $amenities = $units->reduce(function ($counts, $unit) {
                            $unitAmenities = json_decode($unit->amenities, true) ?? [];
                            foreach ($unitAmenities as $amenity) {
                                $counts[$amenity] = ($counts[$amenity] ?? 0) + 1;
                            }
                            return $counts;
                        }, []);

                        return [
                            'ownerName' => $units->first()->owner_name,
                            'propertyId' => $units->first()->property_id,
                            'propertyName' => $units->first()->property_name,
                            'totalUnits' => $totalUnits,
                            
                            // Payment Status
                            'paid' => $statusCounts['paid'],
                            'unpaid' => $statusCounts['unpaid'],
                            'available' => $totalUnits - $rentedCount,
                            'rented' => $rentedCount,
                            'real_rented' => $statusCounts['real_rented'],
                            
                            // Unit Status
                            'building' => $statusCounts['building'],
                            'maintenance' => $statusCounts['maintenance'],
                            
                            // Rent Status
                            'active' => $statusCounts['active'],
                            'late' => $statusCounts['late'],
                            'grace' => $statusCounts['grace'],
                            'cancelled' => $statusCounts['cancelled'],
                            'expired' => $statusCounts['expired'],
                            
                            // Unit Details
                            'totalPrice' => $financials['totalPrice'],
                            'totalCommission' => $financials['totalCommission'],
                            'averagePrice' => $totalUnits > 0 ? $financials['totalPrice'] / $totalUnits : 0,
                            'averageCommission' => $totalUnits > 0 ? $financials['totalCommission'] / $totalUnits : 0,
                            
                            // Unit Types
                            'byBedrooms' => $byBedrooms,
                            'byBathrooms' => $byBathrooms,
                            
                            // Amenities
                            'amenities' => $amenities,
                            
                            // Rates
                            'occupancyRate' => $totalUnits > 0 ? ($rentedCount / $totalUnits) * 100 : 0,
                            'maintenanceRate' => $totalUnits > 0 ? ($statusCounts['maintenance'] / $totalUnits) * 100 : 0,
                            'buildingRate' => $totalUnits > 0 ? ($statusCounts['building'] / $totalUnits) * 100 : 0,
                            'revenueRate' => $totalUnits > 0 ? ($financials['totalPrice'] / $totalUnits) * ($rentedCount / $totalUnits) : 0
                        ];
                    });
            });

        // Optimize summary calculations
        $flattenedUnits = $units->flatMap(fn($units) => $units->values());

        $data = [
            'invoices' => $units->sortBy('name'),
            'type' => 'occupancy',
            'outstanding' => $flattenedUnits->sum('unpaid'),
            'paid' => $flattenedUnits->sum('paid'),
            'total' => $flattenedUnits->sum('totalUnits'),
            'available' => $flattenedUnits->sum('available'),
            'rented' => $flattenedUnits->sum('rented'),
            'real_rented' => $flattenedUnits->sum('real_rented'),
            'lateDays' => 0,
            'properties' => Property::select(['id', 'name'])->get(),
            'owners' => Client::select(['id', 'display_name'])->get(),
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

        return Inertia::render('Rents/Reports/Occupancy', $data);
    }
} 