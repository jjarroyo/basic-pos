<?php

namespace App\Services;

class NetworkHelper
{
    /**
     * Get the local IP address of the machine
     */
    public static function getLocalIp(): string
    {
        // Try to get from config first
        $configIp = config('pos.server_ip');
        if ($configIp && $configIp !== 'localhost' && $configIp !== '127.0.0.1' && $configIp !== '0.0.0.0') {
            return $configIp;
        }

        // Try to detect automatically
        $output = shell_exec('ipconfig');
        
        if ($output) {
            // Look for IPv4 Address
            if (preg_match('/IPv4.*?:\s*(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                $ip = $matches[1];
                
                // Skip localhost
                if ($ip !== '127.0.0.1') {
                    return $ip;
                }
            }
        }

        // Fallback to localhost
        return '127.0.0.1';
    }

    /**
     * Get Reverb configuration with dynamic IP
     */
    public static function getReverbConfig(): array
    {
        $localIp = self::getLocalIp();

        return [
            'app_id' => env('REVERB_APP_ID', '123456'),
            'app_key' => env('REVERB_APP_KEY'),
            'app_secret' => env('REVERB_APP_SECRET'),
            'host' => $localIp,
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
        ];
    }
}
