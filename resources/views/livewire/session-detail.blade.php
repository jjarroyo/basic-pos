<div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('sessions.history') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors mb-4">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-bold">Volver al Historial</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Detalle de Sesión</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">
                        {{ $session->cashRegister->name }} - {{ $session->user->name }}
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">
                        {{ $session->created_at->format('d/m/Y H:i') }} - {{ $session->closed_at->format('d/m/Y H:i') }}
                    </p>
                    @if($session->closedBy)
                        <p class="text-sm text-slate-500 dark:text-slate-500 mt-1 flex items-center gap-2">
                            <span>Cerrado por: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $session->closedBy->name }}</span></span>
                            @if($session->user_id !== $session->closed_by_user_id)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                    Admin
                                </span>
                            @endif
                        </p>
                    @endif
                </div>
                <span class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold">
                    Cerrada
                </span>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Resumen de Ventas -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-600">receipt_long</span>
                    Resumen de Ventas
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

            <!-- Cierre de Caja -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">calculate</span>
                    Cierre de Caja
                </h2>

                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <p class="text-sm text-blue-700 dark:text-blue-400 mb-1">Apertura de Caja</p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($session->starting_cash, 2) }}</p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-1 font-bold">Efectivo Esperado</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($session->expected_cash, 2) }}</p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-1 font-bold">Efectivo Real Contado</p>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($session->actual_cash, 2) }}</p>
                    </div>

                    <div class="p-4 rounded-xl {{ $session->difference >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                        <p class="text-sm {{ $session->difference >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }} mb-1 font-bold">
                            {{ $session->difference >= 0 ? 'Sobrante' : 'Faltante' }}
                        </p>
                        <p class="text-3xl font-extrabold {{ $session->difference >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ${{ number_format(abs($session->difference), 2) }}
                        </p>
                    </div>

                    @if($session->closing_notes)
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                            <p class="text-sm text-yellow-700 dark:text-yellow-400 mb-1 font-bold">Notas de Cierre</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">{{ $session->closing_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Todas las Ventas -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                Todas las Ventas ({{ $salesCount }})
            </h2>

            @if($sales->count() > 0)
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
                            @foreach($sales as $sale)
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
                    <p>No hay ventas registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>
