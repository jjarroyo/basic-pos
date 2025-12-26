<?php

// Función helper para leer configuración desde archivo JSON externo
function getPosConfig($key, $default = null) {
    static $config = null;
    
    if ($config === null) {
        $configPath = storage_path('app/pos_config.json');
        
        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true) ?? [];
        } else {
            $config = [];
        }
    }
    
    return $config[$key] ?? $default;
}

return [
    /*
    |--------------------------------------------------------------------------
    | POS Operation Mode
    |--------------------------------------------------------------------------
    |
    | This determines how the POS operates:
    | - standalone: Independent, no network connection
    | - server: Shares data with other POS (exposes API)
    | - client: Connects to a server POS
    |
    | NOTA: En NativePHP, la configuración se lee desde storage/app/pos_config.json
    | porque el .env está cacheado en el .exe compilado
    |
    */
    'mode' => getPosConfig('mode', env('POS_MODE', 'standalone')),

    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | When mode is 'client', these settings define the server to connect to
    |
    */
    'server_ip' => getPosConfig('server_ip', env('POS_SERVER_IP')),
    'server_port' => getPosConfig('server_port', env('POS_SERVER_PORT', 8000)),

    /*
    |--------------------------------------------------------------------------
    | Sync Interval
    |--------------------------------------------------------------------------
    |
    | How often (in seconds) to sync with the server when in client mode
    |
    */
    'sync_interval' => env('POS_SYNC_INTERVAL', 30),
];
