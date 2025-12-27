<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Client;
use App\Models\Category;
use App\Models\CashRegisterSession;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SyncController extends Controller
{
    /**
     * Get users for synchronization
     */
    public function getUsers(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $users = User::when($lastSync, function($q) use ($lastSync) {
            return $q->where('updated_at', '>', $lastSync);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get products for synchronization
     */
    public function getProducts(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $products = Product::with('category')
            ->when($lastSync, function($q) use ($lastSync) {
                return $q->where('updated_at', '>', $lastSync);
            })
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get clients for synchronization
     */
    public function getClients(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $clients = Client::when($lastSync, function($q) use ($lastSync) {
            return $q->where('updated_at', '>', $lastSync);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $clients,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get sales for synchronization
     */
    public function getSales(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $sales = Sale::with(['items.product', 'client', 'user'])
            ->when($lastSync, function($q) use ($lastSync) {
                return $q->where('updated_at', '>', $lastSync);
            })
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $sales,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Receive a sale from client
     */
    public function receiveSale(Request $request)
    {
        try {
            $saleData = $request->validate([
                'id' => 'nullable|integer',
                'user_id' => 'required|integer',
                'cash_register_id' => 'required|integer',
                'client_id' => 'required|integer',
                'subtotal' => 'required|numeric',
                'discount_type' => 'nullable|string',
                'discount_value' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric',
                'tax' => 'nullable|numeric',
                'total' => 'required|numeric',
                'payment_method' => 'required|string',
                'cash_received' => 'nullable|numeric',
                'change' => 'nullable|numeric',
                'status' => 'nullable|string',
                'items' => 'required|array',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer',
                'items.*.price' => 'required|numeric',
                'items.*.total' => 'required|numeric',
            ]);
            
            // Create or update sale
            $sale = Sale::updateOrCreate(
                ['id' => $saleData['id'] ?? null],
                [
                    'user_id' => $saleData['user_id'],
                    'cash_register_id' => $saleData['cash_register_id'],
                    'client_id' => $saleData['client_id'],
                    'subtotal' => $saleData['subtotal'],
                    'discount_type' => $saleData['discount_type'] ?? null,
                    'discount_value' => $saleData['discount_value'] ?? 0,
                    'discount_amount' => $saleData['discount_amount'] ?? 0,
                    'tax' => $saleData['tax'] ?? 0,
                    'total' => $saleData['total'],
                    'payment_method' => $saleData['payment_method'],
                    'cash_received' => $saleData['cash_received'] ?? null,
                    'change' => $saleData['change'] ?? null,
                    'status' => $saleData['status'] ?? 'completed',
                    'synced_at' => now(),
                ]
            );

            // Create sale details
            if (isset($saleData['items'])) {
                foreach ($saleData['items'] as $item) {
                    SaleDetail::updateOrCreate(
                        [
                            'sale_id' => $sale->id,
                            'product_id' => $item['product_id'],
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'total' => $item['total'],
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'Sale synced successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticate user for client POS
     */
    public function authUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
        ]);
    }

    /**
     * Receive cash register session from client
     */
    public function receiveSession(Request $request)
    {
        try {
            $sessionData = $request->all();
            
            $session = CashRegisterSession::updateOrCreate(
                ['id' => $sessionData['id'] ?? null],
                [
                    'cash_register_id' => $sessionData['cash_register_id'],
                    'user_id' => $sessionData['user_id'],
                    'closed_by_user_id' => $sessionData['closed_by_user_id'] ?? null,
                    'starting_cash' => $sessionData['starting_cash'],
                    'expected_cash' => $sessionData['expected_cash'] ?? null,
                    'expected_card' => $sessionData['expected_card'] ?? null,
                    'actual_cash' => $sessionData['actual_cash'] ?? null,
                    'difference' => $sessionData['difference'] ?? null,
                    'closing_notes' => $sessionData['closing_notes'] ?? null,
                    'opened_at' => $sessionData['opened_at'],
                    'closed_at' => $sessionData['closed_at'] ?? null,
                    'status' => $sessionData['status'],
                    'synced_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'message' => 'Session synced successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories for synchronization
     */
    public function getCategories(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $categories = Category::when($lastSync, function($q) use ($lastSync) {
            return $q->where('updated_at', '>', $lastSync);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get cash registers for synchronization
     */
    public function getCashRegisters(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        // Obtenemos las cajas. 
        // Nota: Generalmente queremos todas las cajas activas
        $registers = CashRegister::when($lastSync, function($q) use ($lastSync) {
            return $q->where('updated_at', '>', $lastSync);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $registers,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Receive stock update from client and broadcast to all
     */
    public function receiveStockUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'new_stock' => 'required|integer|min:0',
                'updated_by' => 'nullable|string',
            ]);

            // Update product stock on server
            $product = Product::find($validated['product_id']);
            $product->update(['stock' => $validated['new_stock']]);

            // Broadcast to all connected clients
            event(new \App\Events\ProductStockUpdated(
                $validated['product_id'],
                $validated['new_stock'],
                $validated['updated_by'] ?? 'server'
            ));

            return response()->json([
                'success' => true,
                'message' => 'Stock updated and broadcasted',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Receive cash register status update and broadcast to all
     */
    public function receiveCashRegisterStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'cash_register_id' => 'required|integer|exists:cash_registers,id',
                'is_open' => 'required|boolean',
                'user_id' => 'nullable|integer|exists:users,id',
                'session_id' => 'nullable|integer',
            ]);

            // Update cash register status on server
            $cashRegister = CashRegister::find($validated['cash_register_id']);
            $cashRegister->update(['is_open' => $validated['is_open']]);

            // Broadcast to all connected clients
            event(new \App\Events\CashRegisterStatusUpdated(
                $validated['cash_register_id'],
                $validated['is_open'],
                $validated['user_id'] ?? null,
                $validated['session_id'] ?? null
            ));

            return response()->json([
                'success' => true,
                'message' => 'Cash register status updated and broadcasted',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register client connection
     */
    public function connect(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
                'client_name' => 'nullable|string',
            ]);

            $service = app(\App\Services\ClientConnectionService::class);
            $connection = $service->registerConnection(
                $validated['user_id'] ?? null,
                $request,
                $validated['client_name'] ?? null
            );

            return response()->json([
                'success' => true,
                'connection_id' => $connection->connection_id,
                'message' => 'Client connected successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update heartbeat for connection
     */
    public function heartbeat(Request $request)
    {
        try {
            $validated = $request->validate([
                'connection_id' => 'required|string',
            ]);

            $service = app(\App\Services\ClientConnectionService::class);
            $result = $service->heartbeat($validated['connection_id']);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Heartbeat updated',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect client
     */
    public function disconnect(Request $request)
    {
        try {
            $validated = $request->validate([
                'connection_id' => 'required|string',
            ]);

            $service = app(\App\Services\ClientConnectionService::class);
            $result = $service->disconnect($validated['connection_id']);

            return response()->json([
                'success' => true,
                'message' => 'Client disconnected successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get connected clients (admin only)
     */
    public function getConnectedClients()
    {
        try {
            $service = app(\App\Services\ClientConnectionService::class);
            $clients = $service->getConnectedClients();
            $stats = $service->getStatistics();

            return response()->json([
                'success' => true,
                'clients' => $clients,
                'statistics' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
