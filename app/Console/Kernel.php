<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->command('background:generate-invoices')->daily()->at('01:00')->runInBackground();
      $schedule->command('background:generate-invoices --late-fees')->daily()->at('01:00')->runInBackground();
      $schedule->command('background:generate-owner-distributions')->daily()->at('01:00')->runInBackground();
      $schedule->command('background:generate-loan-fees')->daily()->at('01:00')->runInBackground();
      $schedule->command('background:check-expired-rents')->daily()->at('01:00')->runInBackground();
      $schedule->command('background:check-expiring-rents')->monthlyOn(1, '01:00')->runInBackground();
      $schedule->command('backup:clean')->daily()->at('01:00')->runInBackground();
      $schedule->command('backup:run --only-db')->daily()->at('01:30')->onFailure(function () {
        activity()
        ->log("Backup generation failed");
      })
      ->onSuccess(function () {
        activity()
        ->log("Backup generation was complete complete");
      })->runInBackground();
      $schedule->command('background:health-check')->everyMinute()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
