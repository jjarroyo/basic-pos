<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeNetwork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:serve {--no-reverb : Start without Reverb WebSocket server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start PHP server and Reverb accessible from network (server mode)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('pos.mode') !== 'server') {
            $this->error('This command only works in server mode.');
            $this->info('Configure the app as "Server" in network settings first.');
            return 1;
        }

        $basePath = base_path();
        $publicPath = public_path();

        // Start Reverb in background (unless --no-reverb flag is used)
        if (!$this->option('no-reverb')) {
            $this->info('🚀 Starting Reverb WebSocket server on 0.0.0.0:8080...');
            
            $reverbProcess = new Process(
                ['php', 'artisan', 'reverb:start'],
                $basePath,
                ['NATIVE_SERVER_MODE' => 'true']
            );
            
            $reverbProcess->start();
            
            // Give Reverb a moment to start
            sleep(2);
            
            if ($reverbProcess->isRunning()) {
                $this->info('✅ Reverb started successfully');
            } else {
                $this->warn('⚠️  Reverb failed to start, continuing without it...');
            }
        }

        $this->info('🌐 Starting PHP server on 0.0.0.0:8000...');
        $this->info('');
        $this->info('📡 Server accessible at: http://' . $this->getLocalIP() . ':8000');
        $this->info('⚡ Reverb WebSocket at: ws://' . $this->getLocalIP() . ':8080');
        $this->info('');
        $this->info('Press Ctrl+C to stop both servers');
        $this->info('');

        // Start PHP server (this will block)
        passthru(sprintf(
            'set NATIVE_SERVER_MODE=true && php -S 0.0.0.0:8000 -t "%s" "%s"',
            $publicPath,
            $basePath . '/server.php'
        ));

        return 0;
    }

    /**
     * Get local IP address
     */
    private function getLocalIP(): string
    {
        $serverIp = config('pos.server_ip');
        
        if ($serverIp && $serverIp !== 'localhost' && $serverIp !== '127.0.0.1') {
            return $serverIp;
        }

        // Try to get local IP
        $output = shell_exec('ipconfig');
        if (preg_match('/IPv4.*?:\s*(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
            return $matches[1];
        }

        return 'localhost';
    }
}
