<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PrinterController;
use App\Livewire\ActiveSession;
use App\Livewire\CashRegisters;
use App\Livewire\Categories;
use App\Livewire\Clients;
use App\Livewire\CloseCashRegister;
use App\Livewire\InventoryAdjustments;
use App\Livewire\OpenCashRegister;
use App\Livewire\Pos;
use App\Livewire\Products;
use App\Livewire\Reports;
use App\Livewire\SessionDetail;
use App\Livewire\SessionHistory;
use App\Livewire\Settings;
use App\Livewire\SetupWizard;
use App\Livewire\SyncLogs;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Login route with hybrid authentication
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

// Setup wizard route (only accessible if setup not completed)
Route::get('/setup', SetupWizard::class)
    ->middleware(['auth'])
    ->name('setup');

Route::get('/dashboard', function () {
    // Get today's sales grouped by hour (SQLite compatible)
    $sales = \App\Models\Sale::whereDate('created_at', today())
        ->selectRaw("CAST(strftime('%H', created_at) AS INTEGER) as hour, SUM(total) as total")
        ->groupBy('hour')
        ->get();
    
    // Fill all 24 hours with data (0 if no sales)
    $salesToday = array_fill(0, 24, 0);
    foreach ($sales as $sale) {
        $salesToday[$sale->hour] = (float) $sale->total;
    }
    
    return view('dashboard', ['salesToday' => $salesToday]);
})
    ->middleware(['auth', 'verified', 'setup.completed', 'redirect.sellers'])
    ->name('dashboard');

Route::middleware(['auth', 'setup.completed', 'redirect.sellers'])->group(function () {

    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/products', Products::class)->name('products');
    Route::get('/pos', Pos::class)->name('pos');
    Route::get('/cash-registers', CashRegisters::class)->name('cash.index');  
    Route::get('/open-register', OpenCashRegister::class)->name('cash.open');
    Route::get('/close-register', CloseCashRegister::class)->name('cash.close');
    Route::get('/session/{sessionId}', ActiveSession::class)->name('session.active');
    Route::get('/sessions/history', SessionHistory::class)->name('sessions.history');
    Route::get('/sessions/{sessionId}', SessionDetail::class)->name('sessions.detail');
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/inventory/adjustments', InventoryAdjustments::class)->name('inventory.adjustments');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/config', Settings::class)->name('config');
    Route::get('/users', UserManagement::class)->name('users.manage');
    Route::get('/sync-logs', SyncLogs::class)->name('sync.logs');
    Route::get('/print/ticket/{sale}', [PrinterController::class, 'ticket'])->name('print.ticket');
 
    Route::get('/php-info', function () {
        // Lista de extensiones que configuramos en el script PowerShell
        $extensionesEsperadas = [
            'curl', 'fileinfo', 'mbstring', 'openssl',
            'zip', 'mysqli', 'pdo_mysql', 
            'pdo_sqlite', 'sqlite3', 'gd', 'soap', 'sockets'
        ];

        $estadoExtensiones = [];
        $faltantes = [];

        foreach ($extensionesEsperadas as $ext) {
            if (extension_loaded($ext)) {
                $estadoExtensiones[$ext] = '✅ OK';
            } else {
                $estadoExtensiones[$ext] = '❌ FALTA';
                $faltantes[] = $ext;
            }
        }

        return [
            'resumen' => empty($faltantes) ? '✅ TODO PERFECTO' : '⚠️ FALTAN EXTENSIONES',
            
            'sistema' => [
                'version' => phpversion(),
                'arquitectura' => PHP_INT_SIZE * 8 . ' bits', // Debería decir 64 bits
                'tipo_thread' => (PHP_ZTS ? 'Thread Safe (TS)' : 'Non-Thread Safe (NTS)'), // Debería decir NTS
                'archivo_ini' => php_ini_loaded_file(), // ¿Qué archivo de configuración está leyendo?
            ],

            'configuracion_ini' => [
                'memory_limit' => ini_get('memory_limit'), // Debería ser 512M
                'max_execution_time' => ini_get('max_execution_time'), // Debería ser 120
                'extension_dir' => ini_get('extension_dir'), // Debería ser "ext"
            ],

            'extensiones_custom' => $estadoExtensiones,
            
            // Solo mostramos esto si hay problemas
            'debug_extra' => [
                'lista_completa_cargada' => get_loaded_extensions(),
                'ubicacion_php' => PHP_BINARY,
            ]
        ];
    })->name('php-info');


   Route::get('/media/{path}', function ($path) {
        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $absolutePath = $disk->path($path);
            return response()->file($absolutePath);
        }
        abort(404);
    })->where('path', '.*');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
