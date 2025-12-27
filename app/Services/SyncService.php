<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\User;
use App\Models\Product;
use App\Models\Client;
use App\Models\Category;
use App\Models\Sale;
use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SyncService
{
    protected $serverUrl;
    protected $timeout = 10; // seconds

    public function __construct()
    {
        $this->serverUrl = 'http://' . config('pos.server_ip') . ':' . config('pos.server_port');
    }

    /**
     * Check if server is online
     */
    public function isServerOnline(): bool
    {
        try {
            $response = Http::timeout(3)->get($this->serverUrl . '/api/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Pull users from server
     */
    public function pullUsers(): bool
    {
        try {
            $lastSync = $this->getLastSync('users');
            
            $response = Http::timeout($this->timeout)->get($this->serverUrl . '/api/sync/users', [
                'last_sync' => $lastSync,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to pull users: ' . $response->body());
                return false;
            }

            $data = $response->json();
            
            foreach ($data['data'] as $userData) {
                User::updateOrCreate(
                    ['id' => $userData['id']],
                    [
                        'id' => $userData['id'], // Forzar ID del servidor
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => $userData['password'] ?? Hash::make('default'),
                        'synced_at' => now(),
                    ]
                );
                
                // Assign role if exists
                if (isset($userData['role'])) {
                    $user = User::find($userData['id']);
                    if ($user && !$user->hasRole($userData['role'])) {
                        $user->assignRole($userData['role']);
                    }
                }
            }

            $this->setLastSync('users', now());
            Log::info('Users synced successfully: ' . count($data['data']) . ' users');
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error pulling users: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Pull products from server
     */
    public function pullProducts(): bool
    {
        try {
            Log::info('🔄 [SYNC] Iniciando sincronización de productos...');
            
            $lastSync = $this->getLastSync('products');
            Log::info('📅 [SYNC] Última sincronización de productos: ' . ($lastSync ?? 'nunca'));
            
            $url = $this->serverUrl . '/api/sync/products';
            Log::info('🌐 [SYNC] Conectando a: ' . $url);
            
            $response = Http::timeout($this->timeout)->get($url, [
                'last_sync' => $lastSync,
            ]);

            if (!$response->successful()) {
                $error = 'HTTP ' . $response->status() . ': ' . $response->body();
                Log::error('❌ [SYNC] Error al obtener productos: ' . $error);
                return false;
            }

            $data = $response->json();
            $productCount = count($data['data'] ?? []);
            
            Log::info('📦 [SYNC] Productos recibidos: ' . $productCount);
            
            if ($productCount === 0) {
                Log::warning('⚠️ [SYNC] No hay productos para sincronizar');
                $this->setLastSync('products', now());
                return true;
            }
            
            $synced = 0;
            $errors = 0;
            
            foreach ($data['data'] as $productData) {
                try {
                    // Validar campos obligatorios
                    if (empty($productData['id']) || empty($productData['name'])) {
                        Log::warning('⚠️ [SYNC] Producto sin ID o nombre, saltando: ' . json_encode($productData));
                        continue;
                    }
                    
                    Product::updateOrCreate(
                        ['id' => $productData['id']],
                        [
                            'id' => $productData['id'], // Forzar ID del servidor
                            'name' => $productData['name'],
                            'barcode' => $productData['barcode'] ?? null,
                            'category_id' => $productData['category_id'] ?? null,
                            'selling_price' => $productData['selling_price'] ?? 0,
                            'cost_price' => $productData['cost_price'] ?? 0,
                            'stock' => $productData['stock'] ?? 0,
                            'min_stock' => $productData['min_stock'] ?? 0,
                            'description' => $productData['description'] ?? null,
                            'image' => $productData['image'] ?? null,
                            'is_active' => $productData['is_active'] ?? true,
                        ]
                    );
                    $synced++;
                    Log::info('✅ [SYNC] Producto sincronizado: ID ' . $productData['id'] . ' - ' . $productData['name']);
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('❌ [SYNC] Error al sincronizar producto ID ' . ($productData['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    Log::error('📍 [SYNC] Datos del producto: ' . json_encode($productData));
                }
            }

            $this->setLastSync('products', now());
            Log::info('✅ [SYNC] Productos sincronizados: ' . $synced . ' exitosos, ' . $errors . ' errores');
            
            return $errors === 0;

        } catch (\Exception $e) {
            Log::error('❌ [SYNC] Error crítico en pullProducts: ' . $e->getMessage());
            Log::error('📍 [SYNC] Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Pull clients from server
     */
    public function pullClients(): bool
    {
        try {
            $lastSync = $this->getLastSync('clients');
            
            $response = Http::timeout($this->timeout)->get($this->serverUrl . '/api/sync/clients', [
                'last_sync' => $lastSync,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to pull clients: ' . $response->body());
                return false;
            }

            $data = $response->json();
            
            foreach ($data['data'] as $clientData) {
                Client::updateOrCreate(
                    ['id' => $clientData['id']],
                    [
                        'id' => $clientData['id'], // Forzar ID del servidor
                        'name' => $clientData['name'],
                        'identification' => $clientData['identification'] ?? null,
                        'document_type' => $clientData['document_type'] ?? null,
                        'email' => $clientData['email'] ?? null,
                        'phone' => $clientData['phone'] ?? null,
                        'address' => $clientData['address'] ?? null,
                        'is_active' => $clientData['is_active'] ?? true,
                    ]
                );
            }

            $this->setLastSync('clients', now());
            Log::info('Clients synced successfully: ' . count($data['data']) . ' clients');
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error pulling clients: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Pull categories from server
     */
    public function pullCategories(): bool
    {
        try {
            $lastSync = $this->getLastSync('categories');
            
            $response = Http::timeout($this->timeout)->get($this->serverUrl . '/api/sync/categories', [
                'last_sync' => $lastSync,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to pull categories: ' . $response->body());
                return false;
            }

            $data = $response->json();
            
            foreach ($data['data'] as $categoryData) {
                Category::updateOrCreate(
                    ['id' => $categoryData['id']],
                    [
                        'id' => $categoryData['id'], // Forzar ID del servidor
                        'name' => $categoryData['name'],
                        'description' => $categoryData['description'] ?? null,
                        'color' => $categoryData['color'] ?? null,
                        'is_active' => $categoryData['is_active'] ?? true,
                    ]
                );
            }

            $this->setLastSync('categories', now());
            Log::info('Categories synced successfully: ' . count($data['data']) . ' categories');
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error pulling categories: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Push sales to server
     */
    public function pushSales(): bool
    {
        try {
            $unsyncedSales = Sale::with('items')
                ->whereNull('synced_at')
                ->get();

            if ($unsyncedSales->isEmpty()) {
                return true;
            }

            $successCount = 0;

            foreach ($unsyncedSales as $sale) {
                $response = Http::timeout($this->timeout)->post($this->serverUrl . '/api/sync/sale', [
                    'id' => $sale->id,
                    'client_id' => $sale->client_id,
                    'user_id' => $sale->user_id,
                    'cash_register_id' => $sale->cash_register_id,
                    'subtotal' => $sale->subtotal,
                    'discount' => $sale->discount ?? 0,
                    'tax' => $sale->tax ?? 0,
                    'total' => $sale->total,
                    'payment_method' => $sale->payment_method,
                    'items' => $sale->items->map(function($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->subtotal,
                        ];
                    })->toArray(),
                ]);

                if ($response->successful()) {
                    $sale->update(['synced_at' => now()]);
                    $successCount++;
                } else {
                    Log::error('Failed to push sale #' . $sale->id . ': ' . $response->body());
                }
            }

            Log::info('Sales pushed successfully: ' . $successCount . '/' . $unsyncedSales->count());
            
            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Error pushing sales: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Push a single sale immediately to server (for sellers)
     */
    public function pushSaleImmediately(Sale $sale): bool
    {
        try {
            if (!$this->isServerOnline()) {
                Log::warning('Server offline, sale will be synced later: #' . $sale->id);
                return false;
            }

            // Load relationships if not already loaded
            $sale->load('details');

            $response = Http::timeout($this->timeout)->post($this->serverUrl . '/api/sync/sale', [
                'id' => $sale->id,
                'client_id' => $sale->client_id,
                'user_id' => $sale->user_id,
                'cash_register_id' => $sale->cash_register_id,
                'subtotal' => $sale->subtotal,
                'discount_type' => $sale->discount_type ?? null,
                'discount_value' => $sale->discount_value ?? 0,
                'discount_amount' => $sale->discount_amount ?? 0,
                'tax' => $sale->tax ?? 0,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'cash_received' => $sale->cash_received ?? null,
                'change' => $sale->change ?? null,
                'status' => $sale->status ?? 'completed',
                'items' => $sale->details->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total,
                    ];
                })->toArray(),
            ]);

            if ($response->successful()) {
                $sale->update(['synced_at' => now()]);
                Log::info('✅ Sale #' . $sale->id . ' pushed immediately to server');
                return true;
            } else {
                Log::error('❌ Failed to push sale #' . $sale->id . ': ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('❌ Error pushing sale immediately #' . $sale->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Push cash register sessions to server
     */
    public function pushSessions(): bool
    {
        try {
            $unsyncedSessions = CashRegisterSession::whereNull('synced_at')
                ->where('status', 'closed')
                ->get();

            if ($unsyncedSessions->isEmpty()) {
                return true;
            }

            $successCount = 0;

            foreach ($unsyncedSessions as $session) {
                $response = Http::timeout($this->timeout)->post($this->serverUrl . '/api/sync/session', [
                    'id' => $session->id,
                    'cash_register_id' => $session->cash_register_id,
                    'user_id' => $session->user_id,
                    'closed_by_user_id' => $session->closed_by_user_id,
                    'starting_cash' => $session->starting_cash,
                    'expected_cash' => $session->expected_cash,
                    'expected_card' => $session->expected_card,
                    'actual_cash' => $session->actual_cash,
                    'difference' => $session->difference,
                    'closing_notes' => $session->closing_notes,
                    'opened_at' => $session->opened_at,
                    'closed_at' => $session->closed_at,
                    'status' => $session->status,
                ]);

                if ($response->successful()) {
                    $session->update(['synced_at' => now()]);
                    $successCount++;
                } else {
                    Log::error('Failed to push session #' . $session->id . ': ' . $response->body());
                }
            }

            Log::info('Sessions pushed successfully: ' . $successCount . '/' . $unsyncedSessions->count());
            
            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Error pushing sessions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync all data
     */
    public function syncAll(): array
    {
        $results = [
            'online' => $this->isServerOnline(),
            'users' => false,
            'products' => false,
            'categories' => false,
            'clients' => false,
            'cash_registers' => false,
            'sales' => false,
            'sessions' => false,
        ];

        if (!$results['online']) {
            Log::warning('Server is offline, skipping sync');
            return $results;
        }

        // Pull data from server
        $results['users'] = $this->pullUsers();
        $results['categories'] = $this->pullCategories();
        $results['products'] = $this->pullProducts();
        $results['clients'] = $this->pullClients();
        $results['cash_registers'] = $this->pullCashRegisters();
        
        // Push data to server
        $results['sales'] = $this->pushSales();
        $results['sessions'] = $this->pushSessions();

        // Note: Sellers don't pull sales from server, they only push their own sales
        // This is handled by the SaleObserver which auto-pushes when a seller creates a sale
        Log::info('Sync completed. User role: ' . (auth()->check() ? auth()->user()->getRoleNames()->first() : 'guest'));

        return $results;
    }

    /**
     * Get last sync timestamp for a resource
     */
    protected function getLastSync(string $resource): ?string
    {
        $key = "last_sync_{$resource}";
        $value = cache($key);
        return $value ? $value->toISOString() : null;
    }

    /**
     * Set last sync timestamp for a resource
     */
    protected function setLastSync(string $resource, $timestamp): void
    {
        $key = "last_sync_{$resource}";
        cache()->forever($key, $timestamp);
    }

    /**
     * Pull cash registers from server
     */
    public function pullCashRegisters(): bool
    {
        try {
            $lastSync = $this->getLastSync('cash_registers');
            
            $response = Http::timeout($this->timeout)->get($this->serverUrl . '/api/sync/cash-registers', [
                'last_sync' => $lastSync,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to pull cash registers: ' . $response->body());
                return false;
            }

            $data = $response->json();
            
            foreach ($data['data'] as $registerData) {
                CashRegister::updateOrCreate(
                    ['id' => $registerData['id']],
                    [
                        'id' => $registerData['id'], // Forzar ID del servidor
                        'name' => $registerData['name'],
                        'is_active' => $registerData['is_active'] ?? true,
                        'is_open' => $registerData['is_open'] ?? false, 
                        'created_at' => $registerData['created_at'],
                        'updated_at' => $registerData['updated_at'],
                    ]
                );
            }

            $this->setLastSync('cash_registers', now());
            Log::info('Cash Registers synced successfully: ' . count($data['data']));
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error pulling cash registers: ' . $e->getMessage());
            return false;
        }
    }
}
