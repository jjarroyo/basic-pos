<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use App\Models\User;
use App\Services\NetworkHelper;
use App\Services\ServerDiscoveryService;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Artisan::call('migrate', ['--force' => true]);
 
        if (User::count() === 0) { 
            Artisan::call('db:seed', ['--force' => true]);
        }
        
        if (config('pos.mode') === 'server' && php_sapi_name() !== 'cli-server') {
            $this->startNetworkServer();
        }
    
        Window::open()
            ->width(1280)
            ->height(900)
            ->minWidth(800)
            ->minHeight(600)
            ->title('Nexus POS')
            ->rememberState();
    }
    
    /**
     * Start PHP server and Reverb accessible from network (server mode only)
     */
    protected function startNetworkServer(): void
    {
        $serverIp = config('pos.server_ip', '0.0.0.0');
        $serverPort = config('pos.server_port', 8000);

        Log::info("🌐 Starting network server on {$serverIp}:{$serverPort}");

        $basePath = base_path();
        $publicPath = public_path();

        // Start Reverb WebSocket server in background (Phase 2)
        $this->startReverbServer();

        // Start server discovery broadcast (Auto IP detection)
        $this->startServerDiscovery();

        // Start PHP built-in server
        $batchFile = $basePath . '/start-server.bat';
        $batchContent = sprintf(
            "@echo off\nset NATIVE_SERVER_MODE=true\nphp -S %s:%d -t \"%s\" \"%s\"",
            $serverIp,
            $serverPort,
            $publicPath,
            $basePath . '/server.php'
        );
        
        file_put_contents($batchFile, $batchContent);
        
        // Script VBS para ejecución silenciosa (sin ventana negra y sin bloqueo)
        $vbsFile = $basePath . '/start-server.vbs';
        $vbsContent = sprintf(
            'Set WshShell = CreateObject("WScript.Shell")' . "\n" .
            'WshShell.Run """%s""", 0, False',
            $batchFile
        );
        
        file_put_contents($vbsFile, $vbsContent);
        
        // Ejecutar
        exec('wscript.exe "' . $vbsFile . '"');

        Log::info("✅ Network server started successfully on {$serverIp}:{$serverPort}");
    }

    /**
     * Start Reverb WebSocket server in background
     */
    protected function startReverbServer(): void
    {
        try {
            $basePath = base_path();
            
            // Get local IP dynamically
            $localIp = NetworkHelper::getLocalIp();
            
            Log::info("🚀 Starting Reverb WebSocket server on {$localIp}:8080...");

            // Create batch file for Reverb with dynamic IP
            $reverbBatchFile = $basePath . '/start-reverb.bat';
            $reverbBatchContent = sprintf(
                "@echo off\ncd /d \"%s\"\nset NATIVE_SERVER_MODE=true\nset REVERB_SERVER_HOST=%s\nphp artisan reverb:start --host=%s --port=8080",
                $basePath,
                $localIp,
                $localIp
            );
            
            file_put_contents($reverbBatchFile, $reverbBatchContent);

            // Create VBS script for silent execution
            $reverbVbsFile = $basePath . '/start-reverb.vbs';
            $reverbVbsContent = sprintf(
                'Set WshShell = CreateObject("WScript.Shell")' . "\n" .
                'WshShell.Run """%s""", 0, False',
                $reverbBatchFile
            );
            
            file_put_contents($reverbVbsFile, $reverbVbsContent);

            // Execute Reverb
            exec('wscript.exe "' . $reverbVbsFile . '"');

            // Give Reverb a moment to start
            sleep(2);

            Log::info("✅ Reverb WebSocket server started successfully on {$localIp}:8080");
            Log::info("📡 Clients should connect to: ws://{$localIp}:8080");
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to start Reverb: ' . $e->getMessage());
            Log::warning('⚠️  Continuing without Reverb - real-time features will not work');
        }
    }

    /**
     * Start server discovery broadcast
     */
    protected function startServerDiscovery(): void
    {
        try {
            $discoveryService = new ServerDiscoveryService();
            $discoveryService->startBroadcasting();
        } catch (\Exception $e) {
            Log::error('❌ Failed to start server discovery: ' . $e->getMessage());
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {        
        return [
              
        ];
    }
}
