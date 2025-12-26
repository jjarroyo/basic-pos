<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;

class AutoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:autosync {--once : Run sync only once instead of continuous loop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sync data with server (client mode only)';

    /**
     * Execute the console command.
     */
    public function handle(SyncService $sync)
    {
        // Only run in client mode
        if (config('pos.mode') !== 'client') {
            $this->error('AutoSync only works in client mode');
            return 1;
        }

        $interval = config('pos.sync_interval', 30);
        $runOnce = $this->option('once');

        $this->info('Starting AutoSync...');
        $this->info('Mode: ' . config('pos.mode'));
        $this->info('Server: ' . config('pos.server_ip') . ':' . config('pos.server_port'));
        $this->info('Interval: ' . $interval . ' seconds');
        $this->line('');

        do {
            $startTime = microtime(true);
            
            $this->info('[' . now()->format('Y-m-d H:i:s') . '] Starting sync...');

            $results = $sync->syncAll();

            // Display results
            $this->line('  Server: ' . ($results['online'] ? '✓ Online' : '✗ Offline'));
            
            if ($results['online']) {
                $this->line('  Users: ' . ($results['users'] ? '✓' : '✗'));
                $this->line('  Products: ' . ($results['products'] ? '✓' : '✗'));
                $this->line('  Clients: ' . ($results['clients'] ? '✓' : '✗'));
                $this->line('  Sales: ' . ($results['sales'] ? '✓' : '✗'));
                $this->line('  Sessions: ' . ($results['sessions'] ? '✓' : '✗'));
            }

            $elapsed = round(microtime(true) - $startTime, 2);
            $this->info('  Completed in ' . $elapsed . 's');
            $this->line('');

            if (!$runOnce) {
                // Wait for next sync
                $this->comment('Waiting ' . $interval . ' seconds for next sync...');
                sleep($interval);
            }

        } while (!$runOnce);

        return 0;
    }
}
