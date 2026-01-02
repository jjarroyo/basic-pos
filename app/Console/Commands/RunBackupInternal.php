<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class RunBackupInternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-internal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the backup process internally. Do not run manually.';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        Log::info('Internal Backup Command Started.');
        try {
            $backupService->runBackup();
            Log::info('Internal Backup Command Completed.');
        } catch (\Throwable $e) {
            Log::error('Internal Backup Command Failed: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }
}
