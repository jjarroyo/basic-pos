<div class="flex flex-col h-full bg-slate-50 dark:bg-[#0f172a]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Ajuste de Inventario</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Entradas y salidas manuales de mercancía</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm relative">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-orange-600 text-2xl">qr_code_scanner</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Seleccionar Producto</h3>
                    </div>

                    @if(!$selectedProduct)
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-slate-400 material-symbols-outlined">search</span>
                            <input wire:model.live.debounce.300ms="search" 
                                type="text" 
                                class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition-all" 
                                placeholder="Buscar por nombre, código o SKU..."
                            >
                            
                            @if(!empty($searchResults))
                                <div class="absolute w-full mt-2 bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xl z-20 overflow-hidden">
                                    @foreach($searchResults as $result)
                                        <button wire:click="selectProduct({{ $result->id }})" class="w-full text-left p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-4 border-b border-slate-100 dark:border-slate-700 last:border-0">
                                            <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                @if($result->image)
                                                    <x-image-display :path="$result->image" class="w-full h-full object-cover" />
                                                @else
                                                    <span class="material-symbols-outlined text-slate-400 p-2">image</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $result->name }}</div>
                                                <div class="text-xs text-slate-500">Stock actual: {{ $result->stock }}</div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800/30 flex justify-between items-center">
                            <div class="flex gap-4 items-center">
                                <div class="size-16 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden">
                                    @if($selectedProduct->image)
                                        <x-image-display :path="$selectedProduct->image" class="w-full h-full object-cover" />
                                    @else
                                        <div class="flex items-center justify-center h-full"><span class="material-symbols-outlined text-slate-400">image</span></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">Seleccionado</p>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">{{ $selectedProduct->name }}</h4>
                                    <p class="text-sm text-slate-500">{{ $selectedProduct->barcode }}</p>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-6">
                                <div>
                                    <p class="text-sm text-slate-500 mb-1">Stock Actual</p>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $selectedProduct->stock }}</p>
                                </div>
                                <button wire:click="cancelSelection" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                @if($selectedProduct)
                <div class="bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm animate-fade-in-up">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-orange-600 text-2xl">tune</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Detalles del Movimiento</h3>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipo de Ajuste</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input wire:model.live="type" value="in" type="radio" class="peer sr-only"/>
                                    <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all {{ $type === 'in' ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                        <span class="material-symbols-outlined mb-1 text-3xl">add_circle</span>
                                        <span class="font-bold">Entrada (+)</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input wire:model.live="type" value="out" type="radio" class="peer sr-only"/>
                                    <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all {{ $type === 'out' ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                        <span class="material-symbols-outlined mb-1 text-3xl">remove_circle</span>
                                        <span class="font-bold">Salida (-)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Cantidad</label>
                                <div class="flex items-center">
                                    <button wire:click="$decrement('quantity')" type="button" class="size-12 rounded-l-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-900 dark:text-white flex items-center justify-center font-bold text-xl">-</button>
                                    <input wire:model="quantity" type="number" class="h-12 w-full text-center border-y border-x-0 border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-slate-900 dark:text-white font-bold text-lg focus:ring-0">
                                    <button wire:click="$increment('quantity')" type="button" class="size-12 rounded-r-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-900 dark:text-white flex items-center justify-center font-bold text-xl">+</button>
                                </div>
                                @error('quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Razón</label>
                                <select wire:model="reason" class="w-full h-12 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0f172a] px-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                                    @foreach($reasons as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Notas (Opcional)</label>
                            <textarea wire:model="notes" rows="2" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0f172a] px-4 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none resize-none" placeholder="Detalles extra..."></textarea>
                        </div>

                        <button wire:click="saveAdjustment" class="w-full h-12 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-base shadow-lg shadow-orange-600/20 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">check_circle</span>
                            Confirmar Ajuste
                        </button>

                        @if (session()->has('message'))
                            <div class="p-3 bg-green-100 text-green-700 rounded-xl text-center font-bold animate-pulse">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col h-full overflow-hidden">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a] flex justify-between items-center">
                        <h3 class="font-bold text-slate-900 dark:text-white">Historial Reciente</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto max-h-[600px] p-0">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($recentMovements as $move)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $move->product->name ?? 'Producto borrado' }}</span>
                                            <span class="text-xs text-slate-500">{{ $move->created_at->format('d/m H:i') }} • {{ $move->reason }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold {{ $move->type == 'in' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ $move->type == 'in' ? '+' : '-' }}{{ $move->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="p-8 text-center text-slate-400 text-sm">
                                        Sin movimientos recientes
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>