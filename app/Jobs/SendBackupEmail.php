<?php

namespace App\Jobs;

use App\Domains\Admin\Services\BackupService;
use App\Domains\Admin\Services\CommandService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBackupEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
      public string $fileName
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BackupService $backupService)
    {
        $backupService->createFile($this->fileName);
    }
}
