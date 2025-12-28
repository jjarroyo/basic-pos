<div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors mb-4">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-bold">Volver al Dashboard</span>
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Cierre de Caja</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $session->cashRegister->name }} - {{ $session->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Resumen de Ventas -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-600">receipt_long</span>
                    Resumen del Día
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">shopping_cart</span>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Total Ventas</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $salesCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-green-600 dark:text-green-400">payments</span>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Ventas en Efectivo</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($cashSales, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">credit_card</span>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Ventas con Tarjeta</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($cardSales, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-200 dark:bg-slate-700 my-2"></div>

                    <div class="flex justify-between items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">attach_money</span>
                            </div>
                            <div>
                                <p class="text-sm text-orange-700 dark:text-orange-400 font-bold">Total del Día</p>
                                <p class="text-2xl font-extrabold text-orange-600 dark:text-orange-400">${{ number_format($totalSales, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteo de Efectivo -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">calculate</span>
                    Conteo de Efectivo
                </h2>

                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <p class="text-sm text-blue-700 dark:text-blue-400 mb-1">Apertura de Caja</p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($session->starting_cash, 2) }}</p>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                        <p class="text-sm text-green-700 dark:text-green-400 mb-1">+ Ventas en Efectivo</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($cashSales, 2) }}</p>
                    </div>

                    @if($expensesCash > 0)
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
                        <p class="text-sm text-red-700 dark:text-red-400 mb-1">- Gastos en Efectivo</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">${{ number_format($expensesCash, 2) }}</p>
                    </div>
                    @endif

                    <div class="h-px bg-slate-200 dark:bg-slate-700"></div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-1 font-bold">Efectivo Esperado</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($expectedCash, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Efectivo Real Contado *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">$</span>
                            <input 
                                wire:model.live="actualCash" 
                                type="number" 
                                step="0.01" 
                                class="w-full pl-10 pr-4 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-2xl font-bold text-slate-900 dark:text-white focus:ring-0 focus:border-orange-500 outline-none transition-colors" 
                                placeholder="0.00"
                                autofocus
                            >
                        </div>
                        @error('actualCash') <span class="text-red-500 text-sm font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($actualCash !== '')
                        <div class="p-4 rounded-xl {{ $difference >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                            <p class="text-sm {{ $difference >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }} mb-1 font-bold">
                                {{ $difference >= 0 ? 'Sobrante' : 'Faltante' }}
                            </p>
                            <p class="text-3xl font-extrabold {{ $difference >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                ${{ number_format(abs($difference), 2) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Expenses Section --}}
        @if($totalExpenses > 0)
        <div class="mt-6 bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600">payments</span>
                Resumen de Gastos
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
                    <p class="text-sm text-red-700 dark:text-red-400 mb-1">Gastos en Efectivo</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">${{ number_format($expensesCash, 0, ',', '.') }}</p>
                </div>

                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <p class="text-sm text-orange-700 dark:text-orange-400 mb-1">Otros Métodos</p>
                    <p class="text-xl font-bold text-orange-600 dark:text-orange-400">${{ number_format($expensesOther, 0, ',', '.') }}</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-1 font-bold">Total Gastos</p>
                    <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($totalExpenses, 0, ',', '.') }}</p>
                </div>
            </div>

            @if(count($expensesByCategory) > 0)
            <div class="mt-4">
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Desglose por Categoría</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($expensesByCategory as $expense)
                    <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded text-sm">
                        <div class="text-slate-600 dark:text-slate-400">{{ $expense['name'] }}</div>
                        <div class="font-bold text-slate-900 dark:text-white">${{ number_format($expense['total'], 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Profitability Section --}}
        <div class="mt-6 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">trending_up</span>
                Rentabilidad del Día
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                    <p class="text-sm text-blue-100 mb-1">Ingresos</p>
                    <p class="text-2xl font-bold">${{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>

                <div class="p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                    <p class="text-sm text-blue-100 mb-1">Gastos</p>
                    <p class="text-2xl font-bold">${{ number_format($totalExpenses, 0, ',', '.') }}</p>
                </div>

                <div class="p-4 bg-white/20 backdrop-blur-sm rounded-xl border-2 border-white/30">
                    <p class="text-sm text-blue-100 mb-1 font-bold">Utilidad Neta</p>
                    <p class="text-3xl font-extrabold {{ $netProfit >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        ${{ number_format($netProfit, 0, ',', '.') }}
                    </p>
                    @if($totalSales > 0)
                    <p class="text-xs text-blue-100 mt-1">
                        Margen: {{ number_format(($netProfit / $totalSales) * 100, 1) }}%
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notas de Cierre --}}
        <div class="mt-6 bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                Notas de Cierre (Opcional)
            </label>
            <textarea 
                wire:model="closingNotes" 
                rows="3" 
                class="w-full rounded-xl border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-3 focus:ring-0 focus:border-orange-500 outline-none transition-colors resize-none" 
                placeholder="Ej: Faltante debido a cambio incorrecto en venta #123..."
            ></textarea>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-6 flex gap-4">
            <a href="{{ route('dashboard') }}" 
               class="flex-1 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold text-center hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                Cancelar
            </a>
            <button 
                wire:click="closeSession"
                wire:loading.attr="disabled"
                class="flex-[2] py-4 rounded-xl bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-lg shadow-lg shadow-orange-600/30 transition-all flex items-center justify-center gap-2">
                <span wire:loading.remove>Cerrar Caja</span>
                <span wire:loading>Cerrando...</span>
                <span class="material-symbols-outlined">lock</span>
            </button>
        </div>
    </div>
</div>
