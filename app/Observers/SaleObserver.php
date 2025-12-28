<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Log sale creation
        if (auth()->check()) {
            Log::info('Sale #' . $sale->id . ' created by user: ' . auth()->user()->email);
        }
    }
}
