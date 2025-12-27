<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\SyncService;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     * Auto-push sales to server when created by a seller
     */
    public function created(Sale $sale): void
    {
        // Only auto-push if user is a seller and server is configured
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();
        
        // Check if user has seller role
        if ($user->hasRole('seller')) {
            Log::info('🔄 [SALE] Vendedor creó venta #' . $sale->id . ', enviando al servidor...');
            
            try {
                $syncService = app(SyncService::class);
                $result = $syncService->pushSaleImmediately($sale);
                
                if ($result) {
                    Log::info('✅ [SALE] Venta #' . $sale->id . ' enviada exitosamente al servidor');
                } else {
                    Log::warning('⚠️ [SALE] Venta #' . $sale->id . ' no pudo enviarse, se sincronizará después');
                }
            } catch (\Exception $e) {
                Log::error('❌ [SALE] Error al enviar venta #' . $sale->id . ': ' . $e->getMessage());
            }
        }
    }
}
