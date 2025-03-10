<?php

namespace App\Console\Commands;

use App\Services\PropertyUnitSnapshotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePropertyUnitSnapshots extends Command
{
    protected $signature = 'property:generate-snapshots {--date= : The date to generate snapshots for (defaults to today)}';
    protected $description = 'Generate end-of-month snapshots for all property units';

    public function handle(PropertyUnitSnapshotService $snapshotService)
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        
        $this->info("Generating snapshots for {$date->format('F Y')}...");
        
        $snapshots = $snapshotService->createMonthEndSnapshots($date);
        
        $this->info("Successfully generated {$snapshots->count()} snapshots.");
    }
} 