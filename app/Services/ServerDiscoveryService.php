<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ServerDiscoveryService
{
    protected $broadcastPort = 8888;
    protected $discoveryPort = 8889;

    /**
     * Start broadcasting server presence (for server mode)
     */
    public function startBroadcasting(): void
    {
        try {
            $localIp = NetworkHelper::getLocalIp();
            $serverPort = config('pos.server_port', 8000);
            
            Log::info("📡 Starting server discovery broadcast on port {$this->broadcastPort}");

            // Create batch file for broadcasting
            $basePath = base_path();
            $broadcastBatchFile = $basePath . '/start-broadcast.bat';
            
            // PHP script to broadcast server presence
            $phpScript = <<<PHP
<?php
\$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option(\$socket, SOL_SOCKET, SO_BROADCAST, 1);

\$message = json_encode([
    'type' => 'pos_server',
    'ip' => '{$localIp}',
    'port' => {$serverPort},
    'reverb_port' => 8080,
    'timestamp' => time()
]);

while (true) {
    socket_sendto(\$socket, \$message, strlen(\$message), 0, '255.255.255.255', {$this->broadcastPort});
    sleep(5); // Broadcast every 5 seconds
}
PHP;

            // Save PHP script
            $scriptFile = $basePath . '/broadcast-server.php';
            file_put_contents($scriptFile, $phpScript);

            // Create batch file
            $batchContent = sprintf(
                "@echo off\ncd /d \"%s\"\nphp \"%s\"",
                $basePath,
                $scriptFile
            );
            
            file_put_contents($broadcastBatchFile, $batchContent);

            // Create VBS for silent execution
            $vbsFile = $basePath . '/start-broadcast.vbs';
            $vbsContent = sprintf(
                'Set WshShell = CreateObject("WScript.Shell")' . "\n" .
                'WshShell.Run """%s""", 0, False',
                $broadcastBatchFile
            );
            
            file_put_contents($vbsFile, $vbsContent);

            // Execute
            exec('wscript.exe "' . $vbsFile . '"');

            Log::info("✅ Server discovery broadcast started on {$localIp}");
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to start server discovery: ' . $e->getMessage());
        }
    }

    /**
     * Discover server in the network (for client mode)
     */
    public function discoverServer(int $timeoutSeconds = 10): ?array
    {
        try {
            Log::info("🔍 Searching for POS server in network...");

            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $timeoutSeconds, 'usec' => 0]);
            socket_bind($socket, '0.0.0.0', $this->broadcastPort);

            $from = '';
            $port = 0;
            $buffer = '';

            // Listen for broadcast messages
            $startTime = time();
            while ((time() - $startTime) < $timeoutSeconds) {
                $bytes = @socket_recvfrom($socket, $buffer, 1024, 0, $from, $port);
                
                if ($bytes === false) {
                    continue;
                }

                $data = json_decode($buffer, true);
                
                if ($data && isset($data['type']) && $data['type'] === 'pos_server') {
                    socket_close($socket);
                    
                    Log::info("✅ Server found at {$data['ip']}:{$data['port']}");
                    
                    return [
                        'ip' => $data['ip'],
                        'port' => $data['port'],
                        'reverb_port' => $data['reverb_port'] ?? 8080,
                    ];
                }
            }

            socket_close($socket);
            Log::warning('⚠️  No server found in network');
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('❌ Error discovering server: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Quick check if server is discoverable
     */
    public function quickDiscover(int $timeoutSeconds = 3): ?array
    {
        return $this->discoverServer($timeoutSeconds);
    }
}
