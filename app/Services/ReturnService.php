<?php

namespace App\Services;

use App\Models\ReturnModel;
use App\Models\ReturnDetail;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\Sale;
use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class ReturnService
{
    /**
     * creates a new return with all its details and processes each item
     */
    public function createReturn($saleId, $items, $reason, $notes = null)
    {
        return DB::transaction(function () use ($saleId, $items, $reason, $notes) {
            // Validate return
            $this->validateReturn($saleId, $items);

            $sale = Sale::findOrFail($saleId);
            
            // Get active cash register session
            $session = CashRegisterSession::where('status', 'open')
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create return record
            $return = ReturnModel::create([
                'sale_id' => $saleId,
                'user_id' => Auth::id(),
                'cash_register_session_id' => $session->id,
                'total_refund' => 0, // Will be calculated
                'payment_method' => $sale->payment_method,
                'reason' => $reason,
                'notes' => $notes,
                'status' => 'completed',
            ]);

            $totalRefund = 0;

            // Process each return item
            foreach ($items as $item) {
                $returnDetail = ReturnDetail::create([
                    'return_id' => $return->id,
                    'sale_detail_id' => $item['sale_detail_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'disposition' => $item['disposition'],
                    'disposition_notes' => $item['disposition_notes'] ?? null,
                    'exchange_product_id' => $item['exchange_product_id'] ?? null,
                    'exchange_quantity' => $item['exchange_quantity'] ?? null,
                    'exchange_unit_price' => $item['exchange_unit_price'] ?? null,
                    'price_difference' => $item['price_difference'] ?? null,
                ]);

                // Process the item based on disposition
                $this->processReturnItem($returnDetail);

                // Calculate refund amount
                if ($returnDetail->disposition === 'exchange') {
                    // For exchanges, only count the price difference
                    $totalRefund += ($returnDetail->price_difference ?? 0);
                } else {
                    // For other dispositions, full refund
                    $totalRefund += $returnDetail->subtotal;
                }
            }

            // Update total refund
            $return->update(['total_refund' => $totalRefund]);

            // Update cash register session
            $this->updateCashRegisterSession($return);

            return $return;
        });
    }

    /**
     * processes a return item based on its disposition type
     */
    protected function processReturnItem(ReturnDetail $returnDetail)
    {
        switch ($returnDetail->disposition) {
            case 'return_to_stock':
                $this->processReturnToStock($returnDetail);
                break;

            case 'exchange':
                $this->processExchange($returnDetail);
                break;

            case 'damaged_with_expense':
                $this->processDamagedWithExpense($returnDetail);
                break;

            case 'damaged_no_expense':
                $this->processDamagedNoExpense($returnDetail);
                break;
        }
    }

    /**
     * returns product to stock and creates inventory movement
     */
    protected function processReturnToStock(ReturnDetail $returnDetail)
    {
        // Increment stock
        $product = Product::findOrFail($returnDetail->product_id);
        $product->increment('stock', $returnDetail->quantity);

        // Create product movement
        ProductMovement::create([
            'product_id' => $returnDetail->product_id,
            'user_id' => Auth::id(),
            'type' => 'in',
            'quantity' => $returnDetail->quantity,
            'reason' => 'return',
            'notes' => "Devolución de venta #{$returnDetail->returnModel->sale_id}",
        ]);
    }

    /**
     * processes product exchange with replacement product
     */
    protected function processExchange(ReturnDetail $returnDetail)
    {
        // 1. Register returned product as damaged (creates expense)
        $this->processDamagedWithExpense($returnDetail);

        // 2. Deduct stock from replacement product
        $exchangeProduct = Product::findOrFail($returnDetail->exchange_product_id);
        $exchangeProduct->decrement('stock', $returnDetail->exchange_quantity);

        // 3. Create product movement for replacement
        ProductMovement::create([
            'product_id' => $returnDetail->exchange_product_id,
            'user_id' => Auth::id(),
            'type' => 'out',
            'quantity' => $returnDetail->exchange_quantity,
            'reason' => 'exchange',
            'notes' => "Cambio por devolución #{$returnDetail->return_id} - Producto original: {$returnDetail->product->name}",
        ]);
    }

    /**
     * registers damaged product as expense
     */
    protected function processDamagedWithExpense(ReturnDetail $returnDetail)
    {
        // Create expense record
        Expense::create([
            'cash_register_session_id' => $returnDetail->returnModel->cash_register_session_id,
            'user_id' => Auth::id(),
            'category' => 'damaged_products',
            'description' => "Producto dañado en devolución: {$returnDetail->product->name}",
            'amount' => $returnDetail->subtotal,
            'reference_type' => 'return',
            'reference_id' => $returnDetail->return_id,
            'payment_method' => 'cash',
        ]);

        // Create product movement
        ProductMovement::create([
            'product_id' => $returnDetail->product_id,
            'user_id' => Auth::id(),
            'type' => 'out',
            'quantity' => $returnDetail->quantity,
            'reason' => 'damaged',
            'notes' => "Producto dañado - Devolución #{$returnDetail->return_id}",
        ]);
    }

    /**
     * registers damaged product without expense tracking
     */
    protected function processDamagedNoExpense(ReturnDetail $returnDetail)
    {
        // Only create product movement (no expense)
        ProductMovement::create([
            'product_id' => $returnDetail->product_id,
            'user_id' => Auth::id(),
            'type' => 'out',
            'quantity' => $returnDetail->quantity,
            'reason' => 'damaged',
            'notes' => "Producto dañado (sin gasto) - Devolución #{$returnDetail->return_id}",
        ]);
    }

    /**
     * updates cash register session balance based on return
     */
    protected function updateCashRegisterSession(ReturnModel $return)
    {
        $session = $return->cashRegisterSession;
        
        // Update calculated cash based on payment method
        if ($return->payment_method === 'cash') {
            // Subtract refund from cash (negative for refunds, positive for price differences)
            $session->decrement('calculated_cash', abs($return->total_refund));
        }
        // For card payments, we don't track physical cash
    }

    /**
     * validates that the return is possible
     */
    protected function validateReturn($saleId, $items)
    {
        $sale = Sale::find($saleId);
        
        if (!$sale) {
            throw new Exception('La venta no existe.');
        }

        // Check if there's an active cash register session
        $session = CashRegisterSession::where('status', 'open')
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            throw new Exception('Debe tener una sesión de caja abierta para procesar devoluciones.');
        }

        foreach ($items as $item) {
            // Find the sale detail
            $saleDetail = $sale->details()
                ->where('id', $item['sale_detail_id'])
                ->where('product_id', $item['product_id'])
                ->first();

            if (!$saleDetail) {
                throw new Exception('El producto no pertenece a esta venta.');
            }

            // Check if trying to return more than purchased
            if ($item['quantity'] > $saleDetail->quantity) {
                throw new Exception("No puede devolver más unidades de las compradas para el producto: {$saleDetail->product->name}");
            }

            // Validate exchange specific requirements
            if ($item['disposition'] === 'exchange') {
                if (empty($item['exchange_product_id'])) {
                    throw new Exception('Debe seleccionar un producto de reemplazo para el cambio.');
                }

                $exchangeProduct = Product::find($item['exchange_product_id']);
                
                if (!$exchangeProduct) {
                    throw new Exception('El producto de reemplazo no existe.');
                }

                if ($exchangeProduct->stock < ($item['exchange_quantity'] ?? 0)) {
                    throw new Exception("No hay suficiente stock del producto de reemplazo: {$exchangeProduct->name}");
                }
            }
        }
    }

    /**
     * calculates price difference for exchange
     */
    public function calculatePriceDifference($returnedPrice, $exchangePrice, $quantity)
    {
        // Positive = customer pays, Negative = refund to customer
        return ($exchangePrice - $returnedPrice) * $quantity;
    }
}
