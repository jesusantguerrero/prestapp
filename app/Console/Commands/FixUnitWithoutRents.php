<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\PropertyUnitService;
use Illuminate\Console\Command;

class FixUnitWithoutRents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-unit-without-rents {teamId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamId = $this->argument('teamId');

        PropertyUnitService::freeUnitsWithBadStatus($teamId);
        return Command::SUCCESS;
    }
}
