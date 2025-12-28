<div class="flex flex-col h-full bg-slate-50 dark:bg-[#101922]">
    
    {{-- Header --}}
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1A2633] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Devoluciones</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Gestiona las devoluciones y cambios de productos</p>
            </div>
        </div>
        
        <button wire:click="openCreateModal" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">add</span>
            Nueva Devolución
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8">
        
        {{-- Filters --}}
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1 max-w-md relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por venta o cliente..." 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
            </div>

            <input wire:model.live="dateFrom" type="date" class="w-full md:w-48 px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
            
            <input wire:model.live="dateTo" type="date" class="w-full md:w-48 px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
        </div>

        {{-- Returns Table --}}
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-[#202e3d] text-slate-500 dark:text-slate-400 font-semibold text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Venta</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($returns as $return)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#253241] transition-colors group">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">#{{ $return->id }}</td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="text-blue-600 dark:text-blue-400 font-mono">#{{ $return->sale_id }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $return->sale->client->name ?? 'Cliente General' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold {{ $return->total_refund < 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format(abs($return->total_refund), 0, ',', '.') }}
                                @if($return->total_refund < 0)
                                    <span class="text-xs">(cobrado)</span>
                                @else
                                    <span class="text-xs">(reembolsado)</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $return->user->name }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="viewReturn({{ $return->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <a href="{{ route('print.return', $return->id) }}" target="_blank" class="p-2 text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">print</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">assignment_return</span>
                            <p>No hay devoluciones registradas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $returns->links() }}
            </div>
        </div>
    </div>

    {{-- Create Return Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-[#1A2633] z-10">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Nueva Devolución</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Busca la venta y selecciona los productos a devolver</p>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    @if(!$selectedSale)
                        {{-- Step 1: Search Sale --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Buscar Venta</label>
                            <div class="flex gap-2">
                                <input wire:model="saleSearch" wire:keydown.enter="searchSale" type="text" placeholder="ID de venta o nombre del cliente..." 
                                    class="flex-1 px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <button wire:click="searchSale" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- Step 2: Sale Info and Product Selection --}}
                        <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div><span class="font-semibold">Venta:</span> #{{ $selectedSale->id }}</div>
                                <div><span class="font-semibold">Cliente:</span> {{ $selectedSale->client->name ?? 'Cliente General' }}</div>
                                <div><span class="font-semibold">Fecha:</span> {{ $selectedSale->created_at->format('d/m/Y H:i') }}</div>
                                <div><span class="font-semibold">Total:</span> ${{ number_format($selectedSale->total, 0, ',', '.') }}</div>
                            </div>
                            <button wire:click="$set('selectedSale', null)" class="mt-2 text-sm text-blue-600 hover:text-blue-700">Cambiar venta</button>
                        </div>

                        {{-- Products from sale --}}
                        <div>
                            <h4 class="font-semibold mb-2 text-slate-900 dark:text-white">Productos de la venta</h4>
                            <div class="space-y-2">
                                @foreach($selectedSale->details as $detail)
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-700 rounded border border-slate-200 dark:border-slate-600">
                                        <div class="flex-1">
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $detail->product->name }}</div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                Cantidad: {{ $detail->quantity }} | Precio: ${{ number_format($detail->unit_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <button wire:click="addItemToReturn({{ $detail->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                            Agregar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Return Items --}}
                        @if(count($returnItems) > 0)
                            <div>
                                <h4 class="font-semibold mb-2 text-slate-900 dark:text-white">Productos a devolver</h4>
                                <div class="space-y-4">
                                    @foreach($returnItems as $index => $item)
                                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="font-medium text-slate-900 dark:text-white">{{ $item['product_name'] }}</div>
                                                <button wire:click="removeItemFromReturn({{ $index }})" class="text-red-600 hover:text-red-700">
                                                    <span class="material-symbols-outlined">close</span>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cantidad a devolver</label>
                                                    <input wire:model.live="returnItems.{{ $index }}.quantity_to_return" type="number" min="1" max="{{ $item['quantity_sold'] }}"
                                                        class="w-full px-3 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Disposición</label>
                                                    <select wire:model.live="returnItems.{{ $index }}.disposition" wire:change="updateDisposition({{ $index }}, $event.target.value)"
                                                        class="w-full px-3 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm">
                                                        <option value="return_to_stock">Retornar a stock</option>
                                                        <option value="exchange">Cambio por otro producto</option>
                                                        <option value="damaged_with_expense">Dañado (con gasto)</option>
                                                        <option value="damaged_no_expense">Dañado (sin gasto)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Exchange fields --}}
                                            @if($item['disposition'] === 'exchange')
                                                <div class="mt-3 p-3 bg-white dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-600">
                                                    <div class="text-sm font-semibold mb-2 text-slate-900 dark:text-white">Producto de reemplazo</div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Producto</label>
                                                            <select wire:model.live="returnItems.{{ $index }}.exchange_product_id" wire:change="selectExchangeProduct({{ $index }}, $event.target.value)"
                                                                class="w-full px-3 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white text-sm">
                                                                <option value="">Seleccionar...</option>
                                                                @foreach($availableProducts as $product)
                                                                    <option value="{{ $product->id }}">
                                                                        {{ $product->name }} - ${{ number_format($product->selling_price, 0) }} (Stock: {{ $product->stock }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cantidad</label>
                                                            <input wire:model.live="returnItems.{{ $index }}.exchange_quantity" wire:change="calculatePriceDifference({{ $index }})" type="number" min="1"
                                                                class="w-full px-3 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white text-sm">
                                                        </div>
                                                    </div>

                                                    @if($item['exchange_product_id'])
                                                        <div class="mt-2 p-2 rounded {{ $item['price_difference'] > 0 ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : ($item['price_difference'] < 0 ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600') }}">
                                                            <div class="text-sm font-semibold">
                                                                Diferencia de precio:
                                                                @if($item['price_difference'] > 0)
                                                                    +${{ number_format($item['price_difference'], 0, ',', '.') }} (Cliente paga)
                                                                @elseif($item['price_difference'] < 0)
                                                                    ${{ number_format(abs($item['price_difference']), 0, ',', '.') }} (Se reembolsa)
                                                                @else
                                                                    Sin diferencia
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mt-3">
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notas</label>
                                                <textarea wire:model="returnItems.{{ $index }}.disposition_notes" rows="2" placeholder="Notas sobre esta devolución..."
                                                    class="w-full px-3 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Return details --}}
                            <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-lg space-y-3">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Motivo de la devolución *</label>
                                    <textarea wire:model="reason" rows="2" placeholder="Especifique el motivo..."
                                        class="w-full px-4 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Notas adicionales</label>
                                    <textarea wire:model="notes" rows="2" placeholder="Notas generales..."
                                        class="w-full px-4 py-2 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white"></textarea>
                                </div>

                                <div class="text-right">
                                    <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                        Total: 
                                        @php $total = $this->calculateTotalRefund(); @endphp
                                        <span class="{{ $total < 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format(abs($total), 0, ',', '.') }}
                                            @if($total > 0)
                                                <span class="text-sm">(a reembolsar)</span>
                                            @elseif($total < 0)
                                                <span class="text-sm">(a cobrar)</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    Cancelar
                                </button>
                                <button wire:click="processReturn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                    Procesar Devolución
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedReturn)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-[#1A2633] z-10">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Devolución #{{ $selectedReturn->id }}</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $selectedReturn->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="font-semibold">Venta:</span> #{{ $selectedReturn->sale_id }}</div>
                        <div><span class="font-semibold">Cliente:</span> {{ $selectedReturn->sale->client->name ?? 'Cliente General' }}</div>
                        <div><span class="font-semibold">Usuario:</span> {{ $selectedReturn->user->name }}</div>
                        <div><span class="font-semibold">Método de pago:</span> {{ $selectedReturn->payment_method === 'cash' ? 'Efectivo' : 'Tarjeta' }}</div>
                    </div>

                    <div>
                        <div class="font-semibold mb-2 text-slate-900 dark:text-white">Productos devueltos</div>
                        <div class="space-y-2">
                            @foreach($selectedReturn->details as $detail)
                                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded">
                                    <div class="flex justify-between">
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $detail->product->name }}</div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                Cantidad: {{ $detail->quantity }} × ${{ number_format($detail->unit_price, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs mt-1">
                                                <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    {{ match($detail->disposition) {
                                                        'return_to_stock' => 'Retornado a stock',
                                                        'exchange' => 'Cambio',
                                                        'damaged_with_expense' => 'Dañado (con gasto)',
                                                        'damaged_no_expense' => 'Dañado (sin gasto)',
                                                    } }}
                                                </span>
                                            </div>
                                            @if($detail->disposition === 'exchange' && $detail->exchangeProduct)
                                                <div class="text-sm mt-2 p-2 bg-white dark:bg-slate-700 rounded">
                                                    <div class="font-medium">→ Reemplazo: {{ $detail->exchangeProduct->name }}</div>
                                                    <div class="text-xs text-slate-600 dark:text-slate-400">
                                                        {{ $detail->exchange_quantity }} × ${{ number_format($detail->exchange_unit_price, 0, ',', '.') }}
                                                        @if($detail->price_difference != 0)
                                                            | Diferencia: ${{ number_format(abs($detail->price_difference), 0, ',', '.') }}
                                                            {{ $detail->price_difference > 0 ? '(cobrado)' : '(reembolsado)' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right font-semibold text-slate-900 dark:text-white">
                                            ${{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded">
                        <div><span class="font-semibold">Motivo:</span> {{ $selectedReturn->reason }}</div>
                        @if($selectedReturn->notes)
                            <div class="mt-2"><span class="font-semibold">Notas:</span> {{ $selectedReturn->notes }}</div>
                        @endif
                    </div>

                    <div class="text-right text-xl font-bold text-slate-900 dark:text-white">
                        Total: 
                        <span class="{{ $selectedReturn->total_refund < 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format(abs($selectedReturn->total_refund), 0, ',', '.') }}
                            {{ $selectedReturn->total_refund > 0 ? '(reembolsado)' : '(cobrado)' }}
                        </span>
                    </div>

                    <div class="flex justify-end">
                        <button wire:click="$set('showDetailModal', false)" class="px-6 py-2 bg-slate-900 dark:bg-slate-700 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
