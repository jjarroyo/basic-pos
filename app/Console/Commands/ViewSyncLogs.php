<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewSyncLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:logs {--lines=50 : Number of lines to show} {--follow : Follow log file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View synchronization logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            $this->error('Log file not found: ' . $logFile);
            return 1;
        }

        if ($this->option('follow')) {
            $this->info('Following sync logs (Ctrl+C to stop)...');
            $this->info('');
            
            passthru('tail -f ' . escapeshellarg($logFile) . ' | grep "\[SYNC\]"');
        } else {
            $lines = (int) $this->option('lines');
            
            $this->info('📋 Last ' . $lines . ' sync log entries:');
            $this->info('');
            
            $command = sprintf(
                'tail -n %d %s | grep "\[SYNC\]"',
                $lines * 3, // Multiplicamos para asegurar que obtenemos suficientes líneas SYNC
                escapeshellarg($logFile)
            );
            
            passthru($command);
        }

        return 0;
    }
}
