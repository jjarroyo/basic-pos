<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Product;
use App\Models\Category;
use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Pos extends Component
{
    public $search = '';
    public $selectedCategory = null;

    public $cart = []; 
    
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $taxRate = 0;  

    public $showCheckoutModal = false;
    public $paymentMethod = 'cash';
    public $cashReceived = ''; 
    public $change = 0;
    public $clientId;
    public $clientName = 'Consumidor Final';
 
    public $clients = [];
    public $showClientModal = false;
    public $newClientName = '';
    public $newClientIdentification = '';
    public $newClientPhone = '';
    public $newClientEmail = '';
 
    public $showDiscountModal = false;
    public $discountType = null;
    public $discountValue = 0;
    public $discountAmount = 0;

    // Keyboard shortcuts support
    public $lastProductAdded = null;
    public $suspendedSales = [];
    public $showHelpModal = false;

    public function mount()
    {
        $activeSession = CashRegisterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$activeSession) {
            return redirect()->route('cash.open');
        }

        // Load all clients
        $this->clients = Client::orderBy('name')->get();

        // Select default client
        $defaultClient = Client::where('identification', '222222222222')->first();
        if ($defaultClient) {
            $this->clientId = $defaultClient->id;
            $this->clientName = $defaultClient->name;
        } elseif ($this->clients->isNotEmpty()) {
            $this->clientId = $this->clients->first()->id;
            $this->clientName = $this->clients->first()->name;
        }
    }

    public function openCheckout()
    {
        if (empty($this->cart)) return;
        
        $this->cashReceived = ''; 
        $this->change = 0;
        $this->paymentMethod = 'cash';
        $this->showCheckoutModal = true;
    }

    public function updatedCashReceived()
    {
        if (is_numeric($this->cashReceived)) {
            $this->change = $this->cashReceived - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function processSale()
    {
        $this->validate([
            'paymentMethod' => 'required',
            'cashReceived' => 'required_if:paymentMethod,cash|numeric|min:' . $this->total,
        ], [
            'cashReceived.min' => 'El monto recibido es insuficiente.',
            'cashReceived.required_if' => 'Ingresa el monto recibido.',
        ]);

        // Get active cash register session
        $session = CashRegisterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$session) {
            session()->flash('error', 'No hay una caja registradora abierta. Por favor, abre una caja antes de realizar ventas.');
            return redirect()->route('cash.open');
        }

        $sale = null;
        DB::transaction(function () use (&$sale, $session) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'cash_register_id' => $session->cash_register_id, 
                'client_id' => $this->clientId,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'discount_type' => $this->discountType,
                'discount_value' => $this->discountValue,
                'discount_amount' => $this->discountAmount,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'cash_received' => $this->paymentMethod == 'cash' ? $this->cashReceived : $this->total,
                'change' => $this->paymentMethod == 'cash' ? $this->change : 0,
            ]);
 
            foreach ($this->cart as $productId => $qty) {
                $product = Product::find($productId);
                
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $product->selling_price,
                    'total' => $qty * $product->selling_price,
                ]);

                // Decrement stock
                $product->decrement('stock', $qty);
            }
        });

        $this->clearCart();
        $this->showCheckoutModal = false;
         
        if ($sale) {
            $this->dispatch('sale-completed', url: route('print.ticket', $sale));
            session()->flash('message', '¡Venta registrada correctamente!');
            
            // Send email notification if enabled
            try {
                $emailEnabled = \App\Models\Setting::get('email_notifications_enabled');
                Log::info("Email notifications enabled: " . $emailEnabled);
                if ($emailEnabled) {
                    $phpBinary = PHP_BINARY;
                    $artisan = base_path('artisan');
                    
                    Log::info("About to create Process with: " . $phpBinary . " " . $artisan . " app:send-sale-receipt " . $sale->id);
                    
                    $process = new \Symfony\Component\Process\Process([
                        $phpBinary, 
                        $artisan, 
                        'app:send-sale-receipt', 
                        $sale->id
                    ]);
                    
                    // Run synchronously (email sending is fast, won't block UI)
                    $process->run();
                    
                    Log::info("Process completed for sale email #" . $sale->id . " - Exit code: " . $process->getExitCode());
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to dispatch email for sale #' . $sale->id . ': ' . $e->getMessage());
            }
        } else {
            session()->flash('error', 'Error al registrar la venta');
        }
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $products = Product::where('is_active', true)
            ->when($this->search, function($q) {
                $q->where(function($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function($q) {
                $q->where('category_id', $this->selectedCategory);
            })
            ->get();

        $cartItems = [];
        if (!empty($this->cart)) {
            $cartProducts = Product::whereIn('id', array_keys($this->cart))->get();
            
            foreach ($cartProducts as $product) {
                $qty = $this->cart[$product->id];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'total' => $product->selling_price * $qty
                ];
            }
        }
        
        $this->calculateTotals($cartItems);

        return view('livewire.pos', [
            'categories' => $categories,
            'products' => $products,
            'cartItems' => $cartItems
        ]);
    }

    public function addToCart($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]++;
        } else {
            $this->cart[$productId] = 1;
        }
        $this->lastProductAdded = $productId;
        Log::info('Cart updated: ' . json_encode($this->cart));
    }

    public function searchByBarcode()
    {
        if (empty($this->search)) {
            return;
        }

        $product = Product::where('barcode', $this->search)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            $this->dispatch('barcode-not-found', barcode: $this->search);
            session()->flash('error', 'Producto no encontrado: ' . $this->search);
            $this->search = '';
            return;
        }

        if ($product->stock <= 0) {
            $this->dispatch('product-no-stock', name: $product->name);
            session()->flash('error', 'Sin stock: ' . $product->name);
            $this->search = '';
            return;
        }

        $this->addToCart($product->id);
        
        $this->dispatch('product-added', name: $product->name);
        
        $this->search = '';
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId] > 1) {
                $this->cart[$productId]--;
            } else {
                unset($this->cart[$productId]);
            }
        }
    }

    public function deleteFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->search = '';
    }

    public function selectCategory($categoryId)
    { 
        $this->selectedCategory = ($this->selectedCategory == $categoryId) ? null : $categoryId;
    }
 

    public function calculateTotals($cartItems)
    {
        $this->subtotal = 0;
        foreach ($cartItems as $item) {
            $this->subtotal += $item['total'];
        }

        $this->tax = 0;
        
        if ($this->discountType === 'percentage') {
            $this->discountAmount = ($this->subtotal * $this->discountValue) / 100;
        } elseif ($this->discountType === 'fixed') {
            $this->discountAmount = min($this->discountValue, $this->subtotal);
        } else {
            $this->discountAmount = 0;
        }
        
        $this->total = $this->subtotal + $this->tax - $this->discountAmount;
    }

    public function selectClient($clientId)
    {
        $client = Client::find($clientId);
        if ($client) {
            $this->clientId = $client->id;
            $this->clientName = $client->name;
        }
    }

    public function createQuickClient()
    {
        $this->validate([
            'newClientName' => 'required|min:3',
            'newClientIdentification' => 'required|min:5|unique:clients,identification',
            'newClientPhone' => 'nullable|max:20',
            'newClientEmail' => 'nullable|email',
        ], [
            'newClientIdentification.required' => 'La identificación es requerida.',
            'newClientIdentification.min' => 'La identificación debe tener al menos 5 caracteres.',
            'newClientIdentification.unique' => 'Esta identificación ya está registrada.',
        ]);
        
        $client = Client::create([
            'name' => $this->newClientName,
            'identification' => $this->newClientIdentification,
            'phone' => $this->newClientPhone,
            'email' => $this->newClientEmail,
        ]);
        
        $this->clients = Client::orderBy('name')->get();
        $this->clientId = $client->id;
        $this->clientName = $client->name;
        
        $this->showClientModal = false;
        $this->resetClientForm();
        
        session()->flash('message', 'Cliente creado exitosamente');
    }

    public function resetClientForm()
    {
        $this->newClientName = '';
        $this->newClientIdentification = '';
        $this->newClientPhone = '';
        $this->newClientEmail = '';
    }

    public function applyDiscount()
    {
        $this->validate([
            'discountType' => 'required|in:percentage,fixed',
            'discountValue' => 'required|numeric|min:0',
        ], [
            'discountType.required' => 'Selecciona un tipo de descuento',
            'discountValue.required' => 'Ingresa el valor del descuento',
            'discountValue.min' => 'El valor debe ser mayor a 0',
        ]);

        if ($this->discountType === 'percentage' && $this->discountValue > 100) {
            $this->addError('discountValue', 'El porcentaje no puede ser mayor a 100%');
            return;
        }

        $this->showDiscountModal = false;
        session()->flash('message', 'Descuento aplicado correctamente');
    }

    public function removeDiscount()
    {
        $this->discountType = null;
        $this->discountValue = 0;
        $this->discountAmount = 0;
    }

    public function openDiscountModal()
    {
        $this->resetValidation();
        $this->discountType = $this->discountType ?? 'percentage';
        $this->discountValue = $this->discountValue ?? 0;
        $this->showDiscountModal = true;
    }

    // Keyboard Shortcuts Methods
    
    public function increaseLastProduct()
    {
        if ($this->lastProductAdded && isset($this->cart[$this->lastProductAdded])) {
            $product = Product::find($this->lastProductAdded);
            if ($product && $this->cart[$this->lastProductAdded] < $product->stock) {
                $this->cart[$this->lastProductAdded]++;
                $this->dispatch('product-quantity-updated', name: $product->name, quantity: $this->cart[$this->lastProductAdded]);
            } else {
                session()->flash('error', 'Stock insuficiente');
            }
        }
    }

    public function decreaseLastProduct()
    {
        if ($this->lastProductAdded && isset($this->cart[$this->lastProductAdded])) {
            if ($this->cart[$this->lastProductAdded] > 1) {
                $this->cart[$this->lastProductAdded]--;
                $product = Product::find($this->lastProductAdded);
                $this->dispatch('product-quantity-updated', name: $product->name, quantity: $this->cart[$this->lastProductAdded]);
            } else {
                unset($this->cart[$this->lastProductAdded]);
                $this->lastProductAdded = null;
                $this->dispatch('product-removed');
            }
        }
    }

    public function suspendCurrentSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'No hay productos en el carrito para suspender');
            return;
        }

        $suspended = [
            'cart' => $this->cart,
            'clientId' => $this->clientId,
            'clientName' => $this->clientName,
            'discountType' => $this->discountType,
            'discountValue' => $this->discountValue,
            'discountAmount' => $this->discountAmount,
            'timestamp' => now()->format('H:i:s'),
            'total' => $this->total
        ];

        $this->suspendedSales[] = $suspended;
        
        $this->clearCart();
        
        session()->flash('message', 'Venta suspendida correctamente. Total: $' . number_format($suspended['total'], 2));
        $this->dispatch('sale-suspended', count: count($this->suspendedSales));
    }

    public function loadSuspendedSale($index)
    {
        if (!isset($this->suspendedSales[$index])) {
            return;
        }

        $suspended = $this->suspendedSales[$index];
        
        $this->cart = $suspended['cart'];
        $this->clientId = $suspended['clientId'];
        $this->clientName = $suspended['clientName'];
        $this->discountType = $suspended['discountType'];
        $this->discountValue = $suspended['discountValue'];
        $this->discountAmount = $suspended['discountAmount'];
        
        unset($this->suspendedSales[$index]);
        $this->suspendedSales = array_values($this->suspendedSales);
        
        session()->flash('message', 'Venta recuperada correctamente');
        $this->dispatch('sale-loaded');
    }

    public function deleteSuspendedSale($index)
    {
        if (isset($this->suspendedSales[$index])) {
            unset($this->suspendedSales[$index]);
            $this->suspendedSales = array_values($this->suspendedSales);
            session()->flash('message', 'Venta suspendida eliminada');
        }
    }

    public function updateProductQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $product = Product::find($productId);
        
        if ($quantity <= 0) {
            unset($this->cart[$productId]);
            if ($this->lastProductAdded == $productId) {
                $this->lastProductAdded = null;
            }
            return;
        }

        if ($product && $quantity <= $product->stock) {
            $this->cart[$productId] = $quantity;
        } else {
            session()->flash('error', 'Stock insuficiente. Disponible: ' . $product->stock);
        }
    }
}