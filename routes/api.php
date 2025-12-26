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
        
        // Receive data endpoints
        Route::post('/sale', [SyncController::class, 'receiveSale']);
        Route::post('/session', [SyncController::class, 'receiveSession']);
        Route::get('/cash-registers', [SyncController::class, 'getCashRegisters']);
    });
    
    // Authentication endpoint
    Route::post('/auth/login', [SyncController::class, 'authUser']);
}
