<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductMovement;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InventoryAdjustments extends Component
{
    public $search = '';
    public $selectedProduct = null;

    public $type = 'in';
    public $quantity = 1;
    public $reason = 'Corrección de inventario';
    public $notes = '';

    public $reasons = [
        'Recepción de proveedor',
        'Corrección de inventario',
        'Devolución de cliente',
        'Producto dañado/merma',
        'Uso interno',
        'Otro'
    ];

    public function render()
    {
        $searchResults = [];
        if (strlen($this->search) > 2 && !$this->selectedProduct) {
            $searchResults = Product::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('barcode', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();
        }

        $recentMovements = ProductMovement::with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.inventory-adjustments', [
            'searchResults' => $searchResults,
            'recentMovements' => $recentMovements
        ]);
    }

    public function selectProduct($id)
    {
        $this->selectedProduct = Product::find($id);
        $this->search = '';
        $this->reset(['quantity', 'notes', 'type']);
    }

    public function cancelSelection()
    {
        $this->reset(['selectedProduct', 'search', 'quantity', 'notes', 'type']);
    }

    public function saveAdjustment()
    {
        $this->validate([
            'selectedProduct' => 'required',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'type' => 'required|in:in,out',
        ]);

        if ($this->type == 'out' && $this->quantity > $this->selectedProduct->stock) {
            $this->addError('quantity', 'No hay suficiente stock para esta salida.');
            return;
        }

        DB::transaction(function () {
            ProductMovement::create([
                'product_id' => $this->selectedProduct->id,
                'user_id' => auth()->id(),
                'type' => $this->type,
                'quantity' => $this->quantity,
                'reason' => $this->reason,
                'notes' => $this->notes,
            ]);

            if ($this->type == 'in') {
                $this->selectedProduct->increment('stock', $this->quantity);
            } else {
                $this->selectedProduct->decrement('stock', $this->quantity);
            }
        });

        session()->flash('message', 'Inventario ajustado correctamente.');
        $this->cancelSelection();
    }
}