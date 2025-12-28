<?php

namespace App\Livewire;

use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\Product;
use App\Services\ReturnService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class Returns extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showDetailModal = false;
    public $selectedReturn = null;

    // Search and filters
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Create return form
    public $saleSearch = '';
    public $selectedSale = null;
    public $returnItems = [];
    public $reason = '';
    public $notes = '';
    public $availableProducts = [];

    protected $returnService;

    public function boot(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $returns = ReturnModel::with(['sale.client', 'user', 'details'])
            ->when($this->search, function ($query) {
                $query->whereHas('sale', function ($q) {
                    $q->where('id', 'like', "%{$this->search}%")
                        ->orWhereHas('client', function ($c) {
                            $c->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.returns', [
            'returns' => $returns,
        ]);
    }

    public function openCreateModal()
    {
        $this->reset(['saleSearch', 'selectedSale', 'returnItems', 'reason', 'notes']);
        $this->showCreateModal = true;
    }

    public function searchSale()
    {
        $this->selectedSale = null;
        $this->returnItems = [];

        if (empty($this->saleSearch)) {
            return;
        }

        // Search by sale ID or client name
        $this->selectedSale = Sale::with(['details.product', 'client'])
            ->where(function ($query) {
                $query->where('id', $this->saleSearch)
                    ->orWhereHas('client', function ($q) {
                        $q->where('name', 'like', "%{$this->saleSearch}%");
                    });
            })
            ->first();

        if (!$this->selectedSale) {
            session()->flash('error', 'No se encontró la venta.');
        }
    }

    public function addItemToReturn($saleDetailId)
    {
        $saleDetail = $this->selectedSale->details->firstWhere('id', $saleDetailId);

        if (!$saleDetail) {
            return;
        }

        // Check if already added
        $existingIndex = collect($this->returnItems)->search(function ($item) use ($saleDetailId) {
            return $item['sale_detail_id'] == $saleDetailId;
        });

        if ($existingIndex !== false) {
            session()->flash('error', 'Este producto ya está en la lista de devolución.');
            return;
        }

        $this->returnItems[] = [
            'sale_detail_id' => $saleDetail->id,
            'product_id' => $saleDetail->product_id,
            'product_name' => $saleDetail->product->name,
            'quantity_sold' => $saleDetail->quantity,
            'quantity_to_return' => 1,
            'unit_price' => $saleDetail->unit_price,
            'disposition' => 'return_to_stock',
            'disposition_notes' => '',
            'exchange_product_id' => null,
            'exchange_product_name' => null,
            'exchange_quantity' => 1,
            'exchange_unit_price' => 0,
            'price_difference' => 0,
        ];

        // Load available products for exchange
        $this->availableProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->get();
    }

    public function removeItemFromReturn($index)
    {
        unset($this->returnItems[$index]);
        $this->returnItems = array_values($this->returnItems);
    }

    public function updateDisposition($index, $disposition)
    {
        if (isset($this->returnItems[$index])) {
            $this->returnItems[$index]['disposition'] = $disposition;

            // Reset exchange fields if not exchange
            if ($disposition !== 'exchange') {
                $this->returnItems[$index]['exchange_product_id'] = null;
                $this->returnItems[$index]['exchange_product_name'] = null;
                $this->returnItems[$index]['exchange_quantity'] = 1;
                $this->returnItems[$index]['exchange_unit_price'] = 0;
                $this->returnItems[$index]['price_difference'] = 0;
            }
        }
    }

    public function selectExchangeProduct($index, $productId)
    {
        if (isset($this->returnItems[$index])) {
            $product = Product::find($productId);

            if ($product) {
                $this->returnItems[$index]['exchange_product_id'] = $product->id;
                $this->returnItems[$index]['exchange_product_name'] = $product->name;
                $this->returnItems[$index]['exchange_unit_price'] = $product->selling_price;
                $this->returnItems[$index]['exchange_quantity'] = $this->returnItems[$index]['quantity_to_return'];

                // Calculate price difference
                $this->calculatePriceDifference($index);
            }
        }
    }

    public function calculatePriceDifference($index)
    {
        if (isset($this->returnItems[$index])) {
            $item = $this->returnItems[$index];
            
            $returnedTotal = $item['unit_price'] * $item['quantity_to_return'];
            $exchangeTotal = $item['exchange_unit_price'] * $item['exchange_quantity'];
            
            $this->returnItems[$index]['price_difference'] = $exchangeTotal - $returnedTotal;
        }
    }

    public function processReturn()
    {
        // Validate
        if (empty($this->returnItems)) {
            session()->flash('error', 'Debe agregar al menos un producto a la devolución.');
            return;
        }

        if (empty($this->reason)) {
            session()->flash('error', 'Debe especificar el motivo de la devolución.');
            return;
        }

        try {
            $return = $this->returnService->createReturn(
                $this->selectedSale->id,
                $this->returnItems,
                $this->reason,
                $this->notes
            );

            session()->flash('success', 'Devolución procesada exitosamente.');
            $this->showCreateModal = false;
            $this->reset(['saleSearch', 'selectedSale', 'returnItems', 'reason', 'notes']);
            
            // Redirect to print receipt
            return redirect()->route('print.return', $return->id);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function viewReturn($returnId)
    {
        $this->selectedReturn = ReturnModel::with(['sale.client', 'user', 'details.product', 'details.exchangeProduct'])
            ->findOrFail($returnId);
        $this->showDetailModal = true;
    }

    public function calculateTotalRefund()
    {
        $total = 0;

        foreach ($this->returnItems as $item) {
            if ($item['disposition'] === 'exchange') {
                // For exchanges, only count price difference
                $total += $item['price_difference'];
            } else {
                // For other dispositions, full refund
                $total += $item['unit_price'] * $item['quantity_to_return'];
            }
        }

        return $total;
    }
}
