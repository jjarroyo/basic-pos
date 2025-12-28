<div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('cash.index') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors mb-4">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-bold">Volver a Cajas</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Sesión Activa</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">
                        {{ $session->cashRegister->name }} - {{ $session->user->name }}
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">
                        Abierta: {{ $session->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <button wire:click="refresh" class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all">
                    <span class="material-symbols-outlined">refresh</span>
                    <span>Actualizar</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Total Ventas -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="size-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-2xl">shopping_cart</span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Total Ventas</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">{{ $salesCount }} {{ $salesCount === 1 ? 'venta' : 'ventas' }}</p>
            </div>

            <!-- Efectivo -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="size-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-2xl">payments</span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Efectivo</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($cashSales, 2) }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">Esperado: ${{ number_format($expectedCash, 2) }}</p>
            </div>

            <!-- Tarjeta -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="size-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-2xl">credit_card</span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Tarjeta</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($cardSales, 2) }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">Pagos electrónicos</p>
            </div>
        </div>

        <!-- Últimas Ventas -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                Últimas Ventas
            </h2>

            @if($recentSales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <th class="text-left py-3 px-4 text-sm font-bold text-slate-600 dark:text-slate-400">Hora</th>
                                <th class="text-left py-3 px-4 text-sm font-bold text-slate-600 dark:text-slate-400">Cliente</th>
                                <th class="text-left py-3 px-4 text-sm font-bold text-slate-600 dark:text-slate-400">Método</th>
                                <th class="text-right py-3 px-4 text-sm font-bold text-slate-600 dark:text-slate-400">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                                <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-slate-900 dark:text-white">
                                        {{ $sale->created_at->format('H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-slate-900 dark:text-white">
                                        {{ $sale->client->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold {{ $sale->payment_method === 'cash' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                            <span class="material-symbols-outlined text-sm">{{ $sale->payment_method === 'cash' ? 'payments' : 'credit_card' }}</span>
                                            {{ $sale->payment_method === 'cash' ? 'Efectivo' : 'Tarjeta' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right text-sm font-bold text-slate-900 dark:text-white">
                                        ${{ number_format($sale->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <span class="material-symbols-outlined text-6xl mb-2 opacity-50">receipt_long</span>
                    <p>No hay ventas registradas aún</p>
                </div>
            @endif
        </div>

        <!-- Acciones -->
        <div class="flex gap-4">
            <a href="{{ route('cash.index') }}" 
               class="flex-1 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold text-center hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                Volver
            </a>
            <a href="{{ route('cash.close', $session->id) }}" 
               class="flex-[2] py-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-lg shadow-lg shadow-red-600/30 transition-all flex items-center justify-center gap-2">
                <span>Cerrar Esta Caja</span>
                <span class="material-symbols-outlined">lock</span>
            </a>
        </div>
    </div>
</div>
