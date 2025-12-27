<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint for network configuration
Route::get('/health', function () {
    Log::info('Health check endpoint accessed');
    return response()->json([
        'status' => 'ok',
        'mode' => config('pos.mode'),
        'timestamp' => now()->toISOString(),
    ]);
});

// Sync endpoints (only active when mode is 'server')
if (config('pos.mode') === 'server') {
    Route::prefix('sync')->group(function () {
        // Get data endpoints
        Route::get('/users', [SyncController::class, 'getUsers']);
        Route::get('/products', [SyncController::class, 'getProducts']);
        Route::get('/categories', [SyncController::class, 'getCategories']);
        Route::get('/clients', [SyncController::class, 'getClients']);
        Route::get('/sales', [SyncController::class, 'getSales']);
        Route::get('/cash-registers', [SyncController::class, 'getCashRegisters']);
        
        // Receive data endpoints
        Route::post('/sale', [SyncController::class, 'receiveSale']);
        Route::post('/session', [SyncController::class, 'receiveSession']);
        
        // Real-time broadcasting endpoints (Phase 2)
        Route::post('/stock-update', [SyncController::class, 'receiveStockUpdate']);
        Route::post('/cash-register-status', [SyncController::class, 'receiveCashRegisterStatus']);
        
        // Client connection tracking endpoints (Phase 3)
        Route::post('/connect', [SyncController::class, 'connect']);
        Route::post('/heartbeat', [SyncController::class, 'heartbeat']);
        Route::post('/disconnect', [SyncController::class, 'disconnect']);
        Route::get('/connected-clients', [SyncController::class, 'getConnectedClients']);
    });
    
    // Authentication endpoint
    Route::post('/auth/login', [SyncController::class, 'authUser']);
}

// Reverb configuration endpoint (available for all modes)
Route::get('/reverb-config', function () {
    $mode = config('pos.mode');
    $serverIp = config('pos.server_ip');
    
    // Determine the correct host to use
    if ($mode === 'client' && $serverIp) {
        // Client mode: use configured server IP
        $host = $serverIp;
    } else {
        // Server or standalone mode: detect local network IP
        $host = \App\Services\NetworkHelper::getLocalIp();
    }
    
    return response()->json([
        'key' => env('REVERB_APP_KEY', 'default-key'),
        'host' => $host,
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
    ]);
});
