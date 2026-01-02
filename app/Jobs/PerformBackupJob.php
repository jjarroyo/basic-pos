<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class PerformBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(BackupService $backupService): void
    {
        Log::info('Background Backup Job Started.');
        try {
            $backupService->runBackup();
            Log::info('Background Backup Job Completed.');
        } catch (\Throwable $e) {
            Log::error('Background Backup Job Failed: ' . $e->getMessage());
        }
    }
}
