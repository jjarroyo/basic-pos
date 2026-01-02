<div class="flex flex-col h-full w-full bg-slate-50 dark:bg-[#0f172a] overflow-hidden">
    
    <header class="flex-none flex items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#1e293b] px-6 py-3 z-20 shadow-sm h-16">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center size-10 bg-orange-100 dark:bg-orange-600/20 text-orange-600 dark:text-orange-500 rounded-xl hover:scale-105 transition-transform" title="Volver al Dashboard">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight">Terminal Punto de Venta</h2>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-gray-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span>Caja Abierta</span>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex flex-1 max-w-xl mx-8">
            <div class="relative w-full text-slate-500 focus-within:text-orange-600 transition-colors">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <kbd class="hidden sm:inline-block px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 rounded text-[10px] font-bold text-slate-500 dark:text-slate-400">TAB</kbd>
                </div>
                <input 
                    id="searchInput"
                    wire:model.live.debounce.150ms="search"
                    wire:keydown.enter="searchByBarcode"
                    class="block w-full rounded-xl border-none bg-slate-100 dark:bg-slate-800 py-2.5 pl-10 pr-12 text-sm placeholder:text-slate-400 focus:ring-2 focus:ring-orange-500/50 transition-all text-slate-900 dark:text-white" 
                    placeholder="Escanear código o buscar producto..." 
                    type="text"
                    autofocus
                />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('cash.close') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-lg">
                <span class="material-symbols-outlined text-[20px]">lock</span>
                <span>Cerrar Caja</span>
            </a>
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-slate-900 dark:text-white leading-tight">{{ auth()->user()->name ?? 'Cajero' }}</p>
                <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">{{ date('d M, H:i') }}</p>
            </div>
            <div class="size-10 bg-orange-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-[#0f172a]">
            
            <div class="sticky top-0 z-10 bg-slate-50 dark:bg-[#0f172a] pt-4 px-6 pb-2">
                <div class="flex gap-3 pb-2 overflow-x-auto py-1 custom-scrollbar">
                    <button wire:click="$set('selectedCategory', null)" 
                        class="flex h-11 shrink-0 items-center justify-center gap-x-2 rounded-xl px-6 shadow-sm transition-all {{ is_null($selectedCategory) ? 'bg-orange-600 text-white shadow-orange-600/30' : 'bg-white dark:bg-[#1e293b] text-slate-700 dark:text-gray-200 hover:border-orange-500/30 border border-transparent' }}">
                        <span class="material-symbols-outlined text-[20px]">sell</span>
                        <span class="text-sm font-bold">Todo</span>
                    </button>

                    @foreach($categories as $cat)
                        <button wire:click="selectCategory({{ $cat->id }})" 
                            class="flex h-11 shrink-0 items-center justify-center gap-x-2 rounded-xl border px-4 shadow-sm transition-all group {{ $selectedCategory == $cat->id ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-[#1e293b] border-transparent hover:border-orange-500/30 text-slate-700 dark:text-gray-200' }}">
                            <span class="size-3 rounded-full" style="background-color: {{ $cat->color }}"></span>
                            <span class="text-sm font-bold">{{ $cat->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="overflow-y-auto p-6 h-full custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 pb-20">
                    @forelse($products as $product)
                        <button 
                            type="button"
                            wire:click="addToCart({{ $product->id }})" 
                            class="group flex flex-col bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm hover:shadow-lg hover:shadow-orange-500/10 transition-all duration-200 cursor-pointer overflow-hidden border {{ isset($cart[$product->id]) ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-transparent hover:border-orange-500/50' }} active:scale-95 text-left relative"
                        >
                            <div class="relative w-full aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if(isset($cart[$product->id]))
                                    <div class="absolute top-2 left-2 z-20 bg-orange-600 text-white text-[12px] font-bold px-2 py-0.5 rounded-full shadow-md border-2 border-white dark:border-[#1e293b] flex items-center gap-1 animate-in fade-in zoom-in duration-200">
                                        <span class="material-symbols-outlined text-[14px]">shopping_cart</span>
                                        {{ $cart[$product->id] }}
                                    </div>
                                @endif
                                
                                @if($product->image)
                                    <x-image-display :path="$product->image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <span class="material-symbols-outlined text-4xl">image_not_supported</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1 shadow-sm z-10
                                    {{ $product->stock <= 5 ? 'bg-red-600 animate-pulse' : ($product->stock <= 10 ? 'bg-amber-500' : 'bg-black/60') }}">
                                    @if($product->stock <= 5)
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                    @endif
                                    {{ $product->stock }} un.
                                </div>
                            </div>
                            
                            <div class="p-3 flex flex-col gap-2">
                                <h3 class="text-slate-900 dark:text-white text-sm font-bold leading-tight line-clamp-2 h-10">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between mt-auto">
                                    <p class="text-slate-700 dark:text-gray-300 text-base font-bold">${{ number_format($product->selling_price, 2) }}</p>
                                    <div class="size-8 rounded-full {{ isset($cart[$product->id]) ? 'bg-orange-600 text-white' : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-orange-600 group-hover:text-white' }} text-slate-900 dark:text-white flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-lg">add</span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-20 text-slate-400">
                            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">search_off</span>
                            <p class="text-lg font-medium">No se encontraron productos.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <aside class="flex w-[400px] flex-col bg-white dark:bg-[#1e293b] border-l border-slate-200 dark:border-slate-700 shadow-2xl z-30">
            <div class="flex-none px-6 pt-6 pb-4 bg-white dark:bg-[#1e293b] z-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-slate-900 dark:text-white text-2xl font-bold leading-tight">Ticket Actual</h2>
                    <div class="flex gap-2">
                        <button wire:click="$set('showHelpModal', true)" class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Atajos de Teclado [F12]">
                            <span class="material-symbols-outlined">keyboard</span>
                        </button>
                        <button wire:click="clearCart" wire:confirm="¿Vaciar todo el carrito?" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors relative group" title="Limpiar Carrito [F4]">
                            <span class="material-symbols-outlined">delete_sweep</span>
                            <span class="absolute -top-1 -right-1 bg-slate-100 dark:bg-slate-700 text-[9px] font-bold px-1 rounded border border-slate-200 dark:border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity">F4</span>
                        </button>
                    </div>
                </div>
                
                <!-- Client Selector -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">
                        Cliente
                    </label>
                    <div class="flex gap-2">
                        <select wire:model.live="clientId" 
                                wire:change="selectClient($event.target.value)"
                                class="flex-1 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-[#1A2633] text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        
                        <button wire:click="$set('showClientModal', true)" 
                                class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-1 flex-shrink-0"
                                title="Crear nuevo cliente [Ctrl+N]">
                            <span class="material-symbols-outlined text-lg">add</span>
                            <span class="text-xs font-bold">Nuevo</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="w-full h-px bg-slate-200 dark:bg-slate-700"></div>

            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-2 custom-scrollbar">
                @forelse($cartItems as $item)
                    <div class="group flex items-start gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow-sm border border-slate-100 dark:border-slate-700 ring-1 ring-transparent hover:ring-orange-500/20">
                        <div class="flex flex-col items-center gap-1 bg-white dark:bg-slate-900 rounded-lg p-1 border border-slate-200 dark:border-slate-700">
                            <button wire:click="addToCart({{ $item['product']->id }})" class="text-slate-400 hover:text-orange-600 transition-colors p-1"><span class="material-symbols-outlined text-sm">add</span></button>
                            <span class="text-sm font-bold w-6 text-center text-slate-900 dark:text-white">{{ $item['quantity'] }}</span>
                            <button wire:click="removeFromCart({{ $item['product']->id }})" class="text-slate-400 hover:text-red-500 transition-colors p-1"><span class="material-symbols-outlined text-sm">remove</span></button>
                        </div>
                        
                        <div class="flex-1 py-1">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-slate-900 dark:text-white font-bold text-sm leading-tight">{{ $item['product']->name }}</h3>
                                <p class="text-slate-800 dark:text-white font-bold">${{ number_format($item['total'], 2) }}</p>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-gray-400">
                                {{ $item['quantity'] }} x ${{ number_format($item['product']->selling_price, 2) }}
                            </p>
                        </div>

                        <button wire:click="deleteFromCart({{ $item['product']->id }})" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2">shopping_cart</span>
                        <p class="text-sm">Carrito vacío</p>
                    </div>
                @endforelse
            </div>

            <div class="flex-none bg-slate-50 dark:bg-[#1e293b] p-6 pt-4 border-t border-slate-200 dark:border-slate-700 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm text-slate-600 dark:text-gray-400 font-medium">
                        <span>Subtotal</span>
                        <span class="text-slate-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($discountAmount > 0)
                        <div class="flex justify-between items-center text-sm bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-medium px-3 py-2 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">sell</span>
                                <span>Descuento 
                                    @if($discountType === 'percentage')
                                        ({{ number_format($discountValue, 0) }}%)
                                    @else
                                        (Fijo)
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold">-${{ number_format($discountAmount, 2) }}</span>
                                <button wire:click="removeDiscount" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Quitar descuento">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <div class="h-px bg-slate-200 dark:bg-slate-700 my-3"></div>
                    <div class="flex justify-between items-end">
                        <span class="text-lg font-bold text-slate-900 dark:text-white">Total</span>
                        <span class="text-3xl font-extrabold text-orange-600 tracking-tight">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @if(!$discountAmount && !empty($cart))
                        <button wire:click="openDiscountModal" class="col-span-1 flex items-center justify-center gap-2 h-12 rounded-xl border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 font-bold text-sm hover:bg-green-50 dark:hover:bg-green-900/20 transition-all relative group">
                            <span class="material-symbols-outlined text-[20px]">sell</span>
                            Descuento
                            <span class="absolute top-1 right-2 text-[10px] opacity-70 bg-green-100 dark:bg-green-900/50 px-1 py-0.5 rounded">F1</span>
                        </button>
                    @else
                        <button wire:click="suspendCurrentSale" wire:confirm="¿Suspender venta actual?" class="col-span-1 flex items-center justify-center gap-2 h-12 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-gray-300 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-all relative group">
                            <span class="material-symbols-outlined text-[20px]">pause</span>
                            Suspender
                            <span class="absolute top-1 right-2 text-[10px] opacity-70 bg-slate-200 dark:bg-slate-700 px-1 py-0.5 rounded">F3</span>
                        </button>
                    @endif
                    
                    <button 
                        wire:click="openCheckout"
                        @if(empty($cart)) disabled @endif
                        class="col-span-{{ !$discountAmount && !empty($cart) ? '1' : '2' }} bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl shadow-orange-600/20 transition-all flex items-center justify-center gap-2 px-6 py-4 mt-1 group relative"
                    >
                        <span>Pagar</span>
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        <span class="absolute top-2 right-2 text-[10px] opacity-70 bg-orange-700 px-1.5 py-0.5 rounded">F8</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
    @if($showCheckoutModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-[#1e293b] w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="px-6 py-4 bg-slate-50 dark:bg-[#0f172a] border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Finalizar Venta</h3>
                    <button wire:click="$set('showCheckoutModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div class="text-center p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Total a Pagar</p>
                            <p class="text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">${{ number_format($total, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Método de Pago</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button wire:click="$set('paymentMethod', 'cash')" 
                                    class="p-4 rounded-xl border-2 flex flex-col items-center gap-2 transition-all {{ $paymentMethod === 'cash' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400' : 'border-slate-200 dark:border-slate-700 hover:border-orange-300 dark:text-slate-300' }}">
                                    <span class="material-symbols-outlined text-3xl">payments</span>
                                    <span class="font-bold">Efectivo</span>
                                </button>
                                <button wire:click="$set('paymentMethod', 'card')" 
                                    class="p-4 rounded-xl border-2 flex flex-col items-center gap-2 transition-all {{ $paymentMethod === 'card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:text-slate-300' }}">
                                    <span class="material-symbols-outlined text-3xl">credit_card</span>
                                    <span class="font-bold">Tarjeta</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @if($paymentMethod === 'cash')
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dinero Recibido</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">$</span>
                                    <input wire:model.live="cashReceived" type="number" step="0.01" class="w-full pl-10 pr-4 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-3xl font-bold text-slate-900 dark:text-white focus:ring-0 focus:border-orange-500 outline-none transition-colors" placeholder="0.00" autofocus>
                                </div>
                                @error('cashReceived') <span class="text-red-500 text-sm font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-between items-center p-4 rounded-xl {{ $change >= 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
                                <span class="font-bold text-lg">{{ $change >= 0 ? 'Cambio:' : 'Faltante:' }}</span>
                                <span class="font-extrabold text-2xl">${{ number_format(abs($change), 2) }}</span>
                            </div>

                            <div class="grid grid-cols-4 gap-2">
                                @foreach([5000, 10000, 20000, 50000, 100000] as $bill)
                                    <button wire:click="$set('cashReceived', {{ $bill }})" class="py-2 bg-slate-100 dark:bg-slate-700 rounded-lg text-slate-700 dark:text-white font-bold hover:bg-slate-200 dark:hover:bg-slate-600 text-sm">
                                        {{ $bill >= 1000 ? number_format($bill / 1000, 0) . 'M' : '$' . $bill }}
                                    </button>
                                @endforeach
                                <button wire:click="$set('cashReceived', {{ $total }})" class="col-span-3 py-2 bg-slate-200 dark:bg-slate-600 rounded-lg text-slate-800 dark:text-white font-bold hover:bg-slate-300 text-sm">
                                    Exacto (${{ number_format($total, 2) }})
                                </button>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-center space-y-4 opacity-70">
                                <span class="material-symbols-outlined text-6xl text-slate-300">point_of_sale</span>
                                <p class="text-slate-500">Procesa el pago en la terminal bancaria por <strong class="text-slate-900 dark:text-white">${{ number_format($total, 2) }}</strong>.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-[#0f172a] border-t border-slate-200 dark:border-slate-700 flex gap-4">
                    <button wire:click="$set('showCheckoutModal', false)" class="flex-1 py-4 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="processSale" 
                        wire:loading.attr="disabled"
                        class="flex-[2] py-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg shadow-lg shadow-orange-600/30 transition-transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Confirmar Venta</span>
                        <span wire:loading>Procesando...</span>
                        <span class="material-symbols-outlined">check_circle</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Client Creation Modal -->
    @if($showClientModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Nuevo Cliente Rápido</h3>
                <button wire:click="$set('showClientModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Identificación (NIT/CC/CE) *
                    </label>
                    <input wire:model="newClientIdentification" type="text" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none"
                           placeholder="Ej: 123456789">
                    @error('newClientIdentification') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nombre Completo *
                    </label>
                    <input wire:model="newClientName" type="text" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none"
                           placeholder="Ej: Juan Pérez">
                    @error('newClientName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>  
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Teléfono (Opcional)
                    </label>
                    <input wire:model="newClientPhone" type="text" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none"
                           placeholder="Ej: 555-1234">
                    @error('newClientPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Email (Opcional)
                    </label>
                    <input wire:model="newClientEmail" type="email" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-green-500 outline-none"
                           placeholder="Ej: juan@example.com">
                    @error('newClientEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 dark:bg-[#202e3d] flex justify-end gap-3">
                <button wire:click="$set('showClientModal', false)" 
                        class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button wire:click="createQuickClient" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg shadow-green-600/30 transition-all">
                    Crear Cliente
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Discount Modal -->
    @if($showDiscountModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Aplicar Descuento</h3>
                <button wire:click="$set('showDiscountModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">
                        Tipo de Descuento *
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-4 rounded-xl border-2 cursor-pointer transition-all {{ $discountType === 'percentage' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-slate-300 dark:border-slate-600 hover:border-green-300' }}">
                            <input type="radio" wire:model.live="discountType" value="percentage" class="sr-only">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-2xl {{ $discountType === 'percentage' ? 'text-green-600 dark:text-green-400' : 'text-slate-600 dark:text-slate-400' }}">percent</span>
                                <p class="text-sm font-bold {{ $discountType === 'percentage' ? 'text-green-700 dark:text-green-400' : 'text-slate-700 dark:text-slate-300' }}">Porcentaje</p>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 rounded-xl border-2 cursor-pointer transition-all {{ $discountType === 'fixed' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-slate-300 dark:border-slate-600 hover:border-green-300' }}">
                            <input type="radio" wire:model.live="discountType" value="fixed" class="sr-only">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-2xl {{ $discountType === 'fixed' ? 'text-green-600 dark:text-green-400' : 'text-slate-600 dark:text-slate-400' }}">payments</span>
                                <p class="text-sm font-bold {{ $discountType === 'fixed' ? 'text-green-700 dark:text-green-400' : 'text-slate-700 dark:text-slate-300' }}">Monto Fijo</p>
                            </div>
                        </label>
                    </div>
                    @error('discountType') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        {{ $discountType === 'percentage' ? 'Porcentaje de Descuento *' : 'Monto del Descuento *' }}
                    </label>
                    <div class="relative">
                        @if($discountType === 'percentage')
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg font-bold">%</span>
                        @else
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg font-bold">$</span>
                        @endif
                        <input 
                            wire:model.live="discountValue" 
                            type="number" 
                            step="0.01" 
                            min="0"
                            class="w-full {{ $discountType === 'fixed' ? 'pl-10' : 'pr-10' }} px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white text-lg font-bold focus:ring-2 focus:ring-green-500 outline-none" 
                            placeholder="{{ $discountType === 'percentage' ? '10' : '5000' }}"
                            autofocus
                        >
                    </div>
                    @error('discountValue') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    
                    @if($discountType && $discountValue > 0)
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-sm text-blue-700 dark:text-blue-400">
                                <span class="font-bold">Vista previa:</span> 
                                @if($discountType === 'percentage')
                                    {{ number_format($discountValue, 0) }}% de descuento = ${{ number_format(($subtotal * $discountValue) / 100, 2) }}
                                @else
                                    Descuento fijo de ${{ number_format($discountValue, 2) }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 dark:bg-[#202e3d] flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                <button wire:click="$set('showDiscountModal', false)" 
                        class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button wire:click="applyDiscount" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg shadow-green-600/30 transition-all">
                    Aplicar Descuento
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Help Modal for Shortcuts -->
    @if($showHelpModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-[#0f172a]">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined">keyboard</span>
                    Atajos de Teclado
                </h3>
                <button wire:click="$set('showHelpModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[70vh] overflow-y-auto">
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Navegación</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Pagar / Checkout</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F8</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Aplicar Descuento</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F1</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Seleccionar Cliente</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F2</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Suspender Venta</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F3</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Limpiar Carrito</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F4</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Ayuda</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">F12</kbd>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Acciones Rápidas</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Buscar Producto</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">TAB</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Aumentar Cantidad</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">+</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Disminuir Cantidad</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">-</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Confirmar / Aceptar</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">Enter</kbd>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-slate-700 dark:text-slate-300">Cancelar / Cerrar</span>
                            <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-white">Esc</kbd>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 dark:bg-[#0f172a] border-t border-slate-200 dark:border-slate-700 text-center">
                <p class="text-sm text-slate-500">Presiona <kbd class="font-bold">Esc</kbd> para cerrar este panel</p>
            </div>
        </div>
    </div>
    @endif

    @script
    <script>
        let barcode = '';
        let barcodeTimeout;
        const BARCODE_TIMEOUT = 100;
        const MIN_BARCODE_LENGTH = 3;

        document.addEventListener('keydown', function(e) {
            // Helpers
            const isInput = e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT';
            const isSearchInput = e.target.id === 'searchInput';

            // Global Shortcuts (Work even in inputs, except some conflicts)
            
            // Tab: Focus Search (Global Override unless already in search)
            if (e.key === 'Tab') {
                if (!isSearchInput) {
                    e.preventDefault();
                    const searchInput = document.getElementById('searchInput');
                     if (searchInput) {
                         searchInput.focus();
                         searchInput.select();
                    }
                }
                return;
            }

            // F1: Discount Modal
            if (e.key === 'F1') {
                e.preventDefault();
                $wire.call('openDiscountModal');
                return;
            }

            // F2: Focus Client Selector
            if (e.key === 'F2') {
                e.preventDefault();
                const clientSelect = document.querySelector('select[wire\\:model*="clientId"]');
                if (clientSelect) clientSelect.focus();
                return;
            }

            // F3: Suspend Sale
            if (e.key === 'F3') {
                e.preventDefault();
                if(confirm('¿Suspender venta actual?')) {
                    $wire.call('suspendCurrentSale');
                }
                return;
            }

            // F4: Clear Cart
            if (e.key === 'F4') {
                e.preventDefault();
                if(confirm('¿Vaciar todo el carrito?')) {
                    $wire.call('clearCart');
                }
                return;
            }

            // F5: Refresh Products (Prevent browser refresh)
            if (e.key === 'F5') {
                e.preventDefault();
                $wire.$refresh();
                return;
            }

            // F8: Checkout
            if (e.key === 'F8') {
                e.preventDefault();
                $wire.call('openCheckout');
                return;
            }

            // F12: Help
            if (e.key === 'F12') {
                e.preventDefault();
                $wire.set('showHelpModal', true);
                return;
            }

            // Esc: Close Modals
            if (e.key === 'Escape') {
                $wire.set('showCheckoutModal', false);
                $wire.set('showClientModal', false);
                $wire.set('showDiscountModal', false);
                $wire.set('showHelpModal', false);
                // Also blur search if focused
                if (isSearchInput) e.target.blur();
                return;
            }

            // Shortcuts disabled when typing in inputs (except search)
            if (isInput && !isSearchInput) {
                // Allow specific navigation in checkout inputs
                if (e.key === 'Enter') {
                    // If in cash received input, confirm sale
                    if (e.target.matches('input[wire\\:model*="cashReceived"]')) {
                        e.preventDefault();
                        $wire.call('processSale');
                    }
                    // If in discount value input, apply
                    if (e.target.matches('input[wire\\:model*="discountValue"]')) {
                         e.preventDefault();
                        $wire.call('applyDiscount');
                    }
                }
                return;
            }

            // Actions not allowed in inputs
            if (!isInput) {
                // Plus: Increase Last Product
                if (e.key === '+' || e.key === '=') {
                    e.preventDefault();
                    $wire.call('increaseLastProduct');
                    return;
                }

                // Minus: Decrease Last Product
                if (e.key === '-') {
                    e.preventDefault();
                    $wire.call('decreaseLastProduct');
                    return;
                }
            }

            // Barcode Scanner Logic (Preserved)
            if (e.key === 'Enter' && barcode.length > 0) {
                 clearTimeout(barcodeTimeout);
                 if (barcode.length >= MIN_BARCODE_LENGTH) {
                     console.log('Código escaneado (Enter):', barcode);
                     $wire.set('search', barcode);
                     // Trigger search
                     setTimeout(() => {
                         $wire.call('searchByBarcode');
                     }, 50);
                 }
                 barcode = '';
                 return;
            }
        });

        document.addEventListener('keypress', function(e) {
            if (e.target.tagName === 'BUTTON') return;
            const isInput = e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA';
            if (isInput && e.target.id !== 'searchInput') return;

            clearTimeout(barcodeTimeout);
            
            // Only capture printable characters for barcode
            if (e.key.length === 1) {
                barcode += e.key;
            }
            
            barcodeTimeout = setTimeout(() => {
                if (barcode.length >= MIN_BARCODE_LENGTH) {
                    console.log('Código escaneado:', barcode);
                    $wire.set('search', barcode);
                    setTimeout(() => {
                         $wire.call('searchByBarcode');
                    }, 50);
                }
                barcode = '';
            }, BARCODE_TIMEOUT);
        });

        $wire.on('sale-completed', (event) => { 
            const width = 400;
            const height = 600;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            window.open(
                event.url, 
                'TicketPrint', 
                `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`
            );
        });

        $wire.on('product-added', (event) => {
            showToast('✓ ' + event.name + ' agregado', 'success');
        });

        $wire.on('barcode-not-found', (event) => {
            showToast('✗ Código no encontrado: ' + event.barcode, 'error');
        });

        $wire.on('product-no-stock', (event) => {
            showToast('⚠ Sin stock: ' + event.name, 'warning');
        });
        
        $wire.on('sale-suspended', (event) => {
            showToast('⏸ Venta suspendida (' + event.count + ' en espera)', 'warning');
        });

        $wire.on('sale-loaded', (event) => {
            showToast('▶ Venta recuperada', 'success');
        });

        $wire.on('product-quantity-updated', (event) => {
            showToast('↺ ' + event.name + ': ' + event.quantity + ' un.', 'success');
        });
        
        $wire.on('product-removed', (event) => {
            showToast('🗑 Producto eliminado', 'warning');
        });

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-20 right-6 z-50 px-6 py-3 rounded-xl shadow-lg font-bold text-sm transition-all transform translate-x-0 ${
                type === 'success' ? 'bg-green-600 text-white' :
                type === 'error' ? 'bg-red-600 text-white' :
                'bg-yellow-600 text-white'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }
    </script>
    @endscript
</div>