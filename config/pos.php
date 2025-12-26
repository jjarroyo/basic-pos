<?php

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
    */
    'mode' => env('POS_MODE', 'standalone'),

    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | When mode is 'client', these settings define the server to connect to
    |
    */
    'server_ip' => env('POS_SERVER_IP'),
    'server_port' => env('POS_SERVER_PORT', 8000),

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
