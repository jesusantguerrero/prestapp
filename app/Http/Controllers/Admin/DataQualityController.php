<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DataQualityController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/DataQuality/Index', [
            'duplicate_clients' => $this->findDuplicateClients(),
            'duplicate_properties' => $this->findDuplicateProperties(),
            'inconsistent_rents' => $this->findInconsistentRents(),
        ]);
    }

    private function findDuplicateClients()
    {
        $clients = Client::select('id', 'display_name', 'email', 'phone', 'dni')
            ->with([
                'rents' => function($query) {
                    $query->with('unit.property');
                }
            ])
            ->where('team_id', request()->user()->current_team_id)
            ->get();

        $processedIds = [];
        $duplicates = [];

        foreach ($clients as $client) {
            // Skip if we've already processed this client as part of another group
            if (in_array($client->id, $processedIds)) {
                continue;
            }

            $similar = $clients->filter(function ($otherClient) use ($client) {
                if ($client->id === $otherClient->id) {
                    return false;
                }

                $percentage = 0;
                $emailPercentage = 0;
                $phonePercentage = 0;

                // Calculate name similarity
                similar_text(
                    Str::lower(trim($client->display_name)),
                    Str::lower(trim($otherClient->display_name)),
                    $percentage
                );

                // If names are exactly the same, consider it a match regardless of other fields
                if ($percentage === 100) {
                    return true;
                }

                // Calculate email similarity if both have emails
                if ($client->email && $otherClient->email) {
                    similar_text(
                        Str::lower(trim($client->email)),
                        Str::lower(trim($otherClient->email)),
                        $emailPercentage
                    );
                }

                // Calculate phone similarity if both have phones
                if ($client->phone && $otherClient->phone) {
                    // Remove non-numeric characters before comparison
                    $phone1 = preg_replace('/[^0-9]/', '', $client->phone);
                    $phone2 = preg_replace('/[^0-9]/', '', $otherClient->phone);
                    
                    if (strlen($phone1) > 0 && strlen($phone2) > 0) {
                        // If phone numbers are exactly the same after cleaning
                        if ($phone1 === $phone2) {
                            $phonePercentage = 100;
                        } else {
                            similar_text($phone1, $phone2, $phonePercentage);
                        }
                    }
                }

                // Weight the scores (name is much more important)
                $totalScore = ($percentage * 0.8) + ($emailPercentage * 0.1) + ($phonePercentage * 0.1);
                
                return $totalScore > 70; // Lower threshold to catch more potential duplicates
            });

            if ($similar->isNotEmpty()) {
                // Add all similar IDs to processed list to avoid duplicates
                $processedIds[] = $client->id;
                foreach ($similar as $s) {
                    $processedIds[] = $s->id;
                }

                // Map similar clients with similarity scores
                $similarClients = $similar->map(function ($similar) use ($client) {
                    $namePercentage = 0;
                    $emailPercentage = 0;
                    $phonePercentage = 0;

                    similar_text(
                        Str::lower(trim($client->display_name)),
                        Str::lower(trim($similar->display_name)),
                        $namePercentage
                    );

                    if ($client->email && $similar->email) {
                        similar_text(
                            Str::lower(trim($client->email)),
                            Str::lower(trim($similar->email)),
                            $emailPercentage
                        );
                    }

                    if ($client->phone && $similar->phone) {
                        $phone1 = preg_replace('/[^0-9]/', '', $client->phone);
                        $phone2 = preg_replace('/[^0-9]/', '', $similar->phone);
                        if ($phone1 === $phone2) {
                            $phonePercentage = 100;
                        } elseif (strlen($phone1) > 0 && strlen($phone2) > 0) {
                            similar_text($phone1, $phone2, $phonePercentage);
                        }
                    }

                    $totalScore = ($namePercentage * 0.8) + ($emailPercentage * 0.1) + ($phonePercentage * 0.1);

                    // Check if client has any rents (active or historical)
                    $hasNoRents = $similar->rents->isEmpty() && $similar->rents->isEmpty();

                    return [
                        'id' => $similar->id,
                        'display_name' => $similar->display_name,
                        'email' => $similar->email,
                        'phone' => $similar->phone,
                        'dni' => $similar->dni,
                        'rents' => $similar->rents->map(function($rent) {
                            return [
                                'id' => $rent->id,
                                'unit_name' => $rent->unit->name,
                                'property_name' => $rent->unit->property->name,
                                'status' => $rent->status
                            ];
                        }),
                        'can_be_deleted' => $hasNoRents,
                        'similarity_scores' => [
                            'name' => round($namePercentage, 1),
                            'email' => round($emailPercentage, 1),
                            'phone' => round($phonePercentage, 1),
                            'total' => round($totalScore, 1)
                        ]
                    ];
                })->values();

                // Check if main client has any rents (active or historical)
                $hasNoRents = $client->rents->isEmpty() && $client->rents->isEmpty();

                $duplicates[] = [
                    'id' => $client->id,
                    'display_name' => $client->display_name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'dni' => $client->dni,
                    'rents' => $client->rents->map(function($rent) {
                        return [
                            'id' => $rent->id,
                            'unit_name' => $rent->unit->name,
                            'property_name' => $rent->unit->property->name,
                            'status' => $rent->status
                        ];
                    }),
                    'can_be_deleted' => $hasNoRents,
                    'similar_clients' => $similarClients,
                ];
            }
        }

        return $duplicates;
    }

    private function findDuplicateProperties()
    {
        $properties = Property::with(['owner', 'rents.client'])
            ->select('id', 'name', 'address', 'owner_id')
            ->where('team_id', request()->user()->current_team_id)
            ->get();

        $processedIds = [];
        $duplicates = [];

        foreach ($properties as $property) {
            // Skip if we've already processed this property as part of another group
            if (in_array($property->id, $processedIds)) {
                continue;
            }

            $similar = $properties->filter(function ($otherProperty) use ($property) {
                if ($property->id === $otherProperty->id) {
                    return false;
                }

                $namePercentage = 0;
                $addressPercentage = 0;

                similar_text(
                    Str::lower($property->name),
                    Str::lower($otherProperty->name),
                    $namePercentage
                );

                similar_text(
                    Str::lower($property->address),
                    Str::lower($otherProperty->address),
                    $addressPercentage
                );

                // Weight the scores (address is more important for properties)
                $totalScore = ($namePercentage * 0.4) + ($addressPercentage * 0.6);
                
                return $totalScore > 80; // 80% similarity threshold
            });

            if ($similar->isNotEmpty()) {
                // Add all similar IDs to processed list to avoid duplicates
                $processedIds[] = $property->id;
                foreach ($similar as $s) {
                    $processedIds[] = $s->id;
                }

                // Map similar properties with similarity scores
                $similarProperties = $similar->map(function ($similar) use ($property) {
                    $namePercentage = 0;
                    $addressPercentage = 0;

                    similar_text(
                        Str::lower($property->name),
                        Str::lower($similar->name),
                        $namePercentage
                    );

                    similar_text(
                        Str::lower($property->address),
                        Str::lower($similar->address),
                        $addressPercentage
                    );

                    $totalScore = ($namePercentage * 0.4) + ($addressPercentage * 0.6);

                    // Get rent history for the property
                    $rentHistory = collect();
                    $rentHistory = $rentHistory->concat($similar->rents->map(function ($rent) {
                        return [
                            'id' => $rent->id,
                            'unit_name' => $rent->unit->name,
                            'tenant_name' => $rent->client->display_name,
                            'start_date' => $rent->start_date,
                            'end_date' => $rent->end_date,
                                'status' => $rent->status,
                            ];
                        }));

                    return [
                        'id' => $similar->id,
                        'name' => $similar->name,
                        'address' => $similar->address,
                        'owner_id' => $similar->owner_id,
                        'owner_name' => $similar->owner->display_name,
                        'units_count' => $similar->units->count(),
                        'rent_history' => $rentHistory->sortByDesc('start_date')->values(),
                        'similarity_scores' => [
                            'name' => round($namePercentage, 1),
                            'address' => round($addressPercentage, 1),
                            'total' => round($totalScore, 1)
                        ]
                    ];
                })->values();

                // Get rent history for the main property
                $rentHistory = collect();
                $rentHistory = $rentHistory->concat($property->rents->map(function ($rent) {
                    return [
                        'id' => $rent->id,
                        'unit_name' => $rent->unit->name,
                        'tenant_name' => $rent->client->display_name,
                        'start_date' => $rent->start_date,
                            'end_date' => $rent->end_date,
                            'status' => $rent->status,
                        ];
                    }));

                $duplicates[] = [
                    'id' => $property->id,
                    'name' => $property->name,
                    'address' => $property->address,
                    'owner_id' => $property->owner_id,
                    'owner_name' => $property->owner->display_name,
                    'units_count' => $property->units->count(),
                    'rent_history' => $rentHistory->sortByDesc('start_date')->values(),
                    'total_instances' => $similar->count() + 1,
                    'similar_properties' => $similarProperties,
                ];
            }
        }

        return $duplicates;
    }

    private function findInconsistentRents()
    {
        $rents = Rent::with(['unit.property', 'client'])
            ->where('team_id', request()->user()->current_team_id)
            ->get();

        $issues = [];

        foreach ($rents as $rent) {
            // Find overlapping rents for the same unit
            $overlapping = $rents->filter(function ($otherRent) use ($rent) {
                if ($rent->id === $otherRent->id || $rent->unit_id !== $otherRent->unit_id) {
                    return false;
                }

                $rentStart = Carbon::parse($rent->start_date);
                $rentEnd = Carbon::parse($rent->end_date);
                $otherStart = Carbon::parse($otherRent->start_date);
                $otherEnd = Carbon::parse($otherRent->end_date);

                return $rentStart->between($otherStart, $otherEnd) ||
                    $rentEnd->between($otherStart, $otherEnd) ||
                    $otherStart->between($rentStart, $rentEnd) ||
                    $otherEnd->between($rentStart, $rentEnd);
            });

            if ($overlapping->isNotEmpty()) {
                $issues[] = [
                    'id' => $rent->id,
                    'unit_id' => $rent->unit_id,
                    'unit_name' => $rent->unit->name,
                    'property_name' => $rent->unit->property->name,
                    'tenant_name' => $rent->client->display_name,
                    'start_date' => $rent->start_date,
                    'end_date' => $rent->end_date,
                    'status' => $rent->status,
                    'issue_type' => 'overlap',
                    'conflicting_rents' => $overlapping->map(function ($conflict) {
                        return [
                            'id' => $conflict->id,
                            'start_date' => $conflict->start_date,
                            'end_date' => $conflict->end_date,
                            'tenant_name' => $conflict->client->display_name,
                        ];
                    })->values(),
                ];
                continue;
            }

            // Find gaps between rents
            $nextRent = $rents->where('unit_id', $rent->unit_id)
                ->where('start_date', '>', $rent->end_date)
                ->sortBy('start_date')
                ->first();

            if ($nextRent) {
                $gap = Carbon::parse($rent->end_date)->diffInDays(Carbon::parse($nextRent->start_date));
                if ($gap > 1) { // More than 1 day gap
                    $issues[] = [
                        'id' => $rent->id,
                        'unit_id' => $rent->unit_id,
                        'unit_name' => $rent->unit->name,
                        'property_name' => $rent->unit->property->name,
                        'tenant_name' => $rent->client->display_name,
                        'start_date' => $rent->start_date,
                        'end_date' => $rent->end_date,
                        'status' => $rent->status,
                        'issue_type' => 'gap',
                        'conflicting_rents' => [[
                            'id' => $nextRent->id,
                            'start_date' => $nextRent->start_date,
                            'end_date' => $nextRent->end_date,
                            'tenant_name' => $nextRent->client->display_name,
                        ]],
                    ];
                }
            }

            // Find duplicate rents (same tenant, unit, and dates)
            $duplicates = $rents->filter(function ($otherRent) use ($rent) {
                if ($rent->id === $otherRent->id) {
                    return false;
                }

                return $rent->unit_id === $otherRent->unit_id &&
                    $rent->tenant_id === $otherRent->tenant_id &&
                    $rent->start_date === $otherRent->start_date &&
                    $rent->end_date === $otherRent->end_date;
            });

            if ($duplicates->isNotEmpty()) {
                $issues[] = [
                    'id' => $rent->id,
                    'unit_id' => $rent->unit_id,
                    'unit_name' => $rent->unit->name,
                    'property_name' => $rent->unit->property->name,
                    'tenant_name' => $rent->client->display_name,
                    'start_date' => $rent->start_date,
                    'end_date' => $rent->end_date,
                    'status' => $rent->status,
                    'issue_type' => 'duplicate',
                    'conflicting_rents' => $duplicates->map(function ($duplicate) {
                        return [
                            'id' => $duplicate->id,
                            'start_date' => $duplicate->start_date,
                            'end_date' => $duplicate->end_date,
                            'tenant_name' => $duplicate->client->display_name,
                        ];
                    })->values(),
                ];
            }
        }

        return $issues;
    }
} 