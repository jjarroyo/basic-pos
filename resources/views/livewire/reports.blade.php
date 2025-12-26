<div class="flex flex-col h-full bg-slate-50 dark:bg-[#0f172a]">
    
    <div class="px-8 py-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reportes de Venta</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Resumen financiero y transacciones</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="dateRange" class="h-11 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold px-4">
                <option value="today">Hoy</option>
                <option value="yesterday">Ayer</option>
                <option value="week">Esta Semana</option>
                <option value="month">Este Mes</option>
                <option value="custom">Personalizado</option>
            </select>

            @if($dateRange === 'custom')
                <div class="flex items-center gap-2">
                    <input wire:model.live="startDate" type="date" class="h-11 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-sm">
                    <span class="text-slate-400">-</span>
                    <input wire:model.live="endDate" type="date" class="h-11 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-sm">
                </div>
            @endif

            <button wire:click="exportExcel" class="h-11 px-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-green-600/20 transition-all">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span class="hidden sm:inline">Exportar Excel</span>
            </button>

            <button onclick="exportToPDF()" class="h-11 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-red-600/20 transition-all">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                <span class="hidden sm:inline">Exportar PDF</span>
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col">
                    <span class="text-slate-500 dark:text-slate-400 text-sm font-bold mb-2">Ventas Totales</span>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        ${{ number_format($totalSales, 2) }}
                    </div>
                    <div class="mt-auto pt-4 flex items-center gap-2 text-xs font-bold text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined text-lg">trending_up</span>
                        <span>Ingresos Brutos</span>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col">
                    <span class="text-slate-500 dark:text-slate-400 text-sm font-bold mb-2">Transacciones</span>
                    <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $totalTransactions }}
                    </div>
                    <div class="mt-auto pt-4 text-xs font-bold text-blue-600 dark:text-blue-400">
                        Ticket Promedio: ${{ number_format($averageTicket, 2) }}
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col">
                    <span class="text-slate-500 dark:text-slate-400 text-sm font-bold mb-2">Desglose Pago</span>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Efectivo</span>
                            <span class="font-bold text-slate-900 dark:text-white">${{ number_format($cashSales, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Tarjeta</span>
                            <span class="font-bold text-slate-900 dark:text-white">${{ number_format($cardSales, 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-auto pt-3">
                        <div class="h-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden flex">
                            @if($totalSales > 0)
                                <div class="h-full bg-green-500" style="width: {{ ($cashSales / $totalSales) * 100 }}%"></div>
                                <div class="h-full bg-blue-500" style="width: {{ ($cardSales / $totalSales) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-600/20 flex flex-col justify-between">
                    <div>
                        <span class="text-blue-100 text-sm font-bold">Estado Actual</span>
                        <div class="text-2xl font-bold mt-1">Reporte Generado</div>
                    </div>
                    <div class="text-xs text-blue-100 opacity-80 mt-4">
                        Rango: {{ \Carbon\Carbon::parse($startDate)->format('d/m') }} al {{ \Carbon\Carbon::parse($endDate)->format('d/m') }}
                    </div>
                </div>
            </div>

            <!-- Sales Trend Chart -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Tendencia de Ventas</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Evolución de ventas en el período seleccionado</p>
                    </div>
                    <select wire:model.live="groupBy" class="h-10 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold px-3">
                        <option value="day">Por Día</option>
                        <option value="week">Por Semana</option>
                        <option value="month">Por Mes</option>
                    </select>
                </div>
                
                <div class="p-6">
                    <canvas id="salesChart" class="w-full" style="max-height: 400px;"></canvas>
                </div>
            </div>

            <!-- Seller Performance Chart -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Rendimiento de Vendedores</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Comparación de ventas por vendedor</p>
                </div>
                
                <div class="p-6">
                    <canvas id="sellerChart" class="w-full" style="max-height: 350px;"></canvas>
                </div>
            </div>

            <!-- Stock Levels Chart -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Niveles de Inventario</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Productos con menor stock disponible</p>
                </div>
                
                <div class="p-6">
                    <canvas id="stockChart" class="w-full" style="max-height: 400px;"></canvas>
                </div>
            </div>

            <!-- Top Products Chart -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Top 10 Productos Más Vendidos</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Productos con mayor cantidad vendida en el período</p>
                </div>
                
                <div class="p-6">
                    <canvas id="topProductsChart" class="w-full" style="max-height: 400px;"></canvas>
                </div>
            </div>

            <!-- Payment Methods Chart -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Distribución de Métodos de Pago</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Proporción de ventas por método de pago</p>
                </div>
                
                <div class="p-6 flex justify-center">
                    <div style="max-width: 350px; width: 100%;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Alert Widgets Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Low Stock Products Widget -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-red-50 dark:bg-red-900/20">
                        <div class="flex items-center gap-3">
                            <div class="size-10 bg-red-100 dark:bg-red-900/40 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">Productos con Bajo Stock</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Requieren reabastecimiento urgente</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($lowStockProducts->count() > 0)
                            <div class="space-y-3">
                                @foreach($lowStockProducts->take(5) as $product)
                                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $product['name'] }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                Stock actual: <span class="font-bold text-red-600 dark:text-red-400">{{ $product['stock'] }}</span> 
                                                / Mínimo: {{ $product['min_stock'] }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Sugerido</p>
                                            <p class="font-bold text-blue-600 dark:text-blue-400">{{ $product['recommended_order'] }} un.</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($lowStockProducts->count() > 5)
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-4 text-center">
                                    Y {{ $lowStockProducts->count() - 5 }} productos más...
                                </p>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <span class="material-symbols-outlined text-4xl text-green-500 mb-2">check_circle</span>
                                <p class="text-slate-500 dark:text-slate-400">Todos los productos tienen stock suficiente</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Slow Moving Products Widget -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-yellow-50 dark:bg-yellow-900/20">
                        <div class="flex items-center gap-3">
                            <div class="size-10 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">schedule</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">Productos de Lenta Rotación</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Sin ventas en los últimos 30 días</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($slowMovingProducts->count() > 0)
                            <div class="space-y-3">
                                @foreach($slowMovingProducts->take(5) as $product)
                                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $product['product'] }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                Stock inmovilizado: {{ $product['stock'] }} unidades
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Valor</p>
                                            <p class="font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($product['inventory_value'], 0) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($slowMovingProducts->count() > 5)
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-4 text-center">
                                    Y {{ $slowMovingProducts->count() - 5 }} productos más...
                                </p>
                            @endif
                            
                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    💡 <strong>Sugerencia:</strong> Considera promociones o descuentos para estos productos
                                </p>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <span class="material-symbols-outlined text-4xl text-green-500 mb-2">trending_up</span>
                                <p class="text-slate-500 dark:text-slate-400">Todos los productos tienen buena rotación</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 dark:text-white">Detalle de Ventas</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-[#0f172a] text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">ID Venta</th>
                                <th class="px-6 py-4">Fecha / Hora</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Vendedor</th>
                                <th class="px-6 py-4">Método</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($sales as $sale)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                    {{ $sale->created_at->format('d M Y') }}
                                    <span class="text-slate-400 text-xs ml-1">{{ $sale->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ $sale->client->name ?? 'Consumidor Final' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ $sale->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold {{ $sale->payment_method == 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $sale->payment_method == 'cash' ? 'payments' : 'credit_card' }}</span>
                                        {{ ucfirst($sale->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-white">
                                    ${{ number_format($sale->total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-blue-600 transition-colors" title="Ver detalles (Próximamente)">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <a href="{{ route('print.ticket', $sale->id) }}" 
                                    target="_blank"
                                    onclick="window.open(this.href, 'TicketPrint', 'width=400,height=600'); return false;"
                                    class="text-slate-400 hover:text-blue-600 transition-colors" 
                                    title="Imprimir Ticket">
                                        <span class="material-symbols-outlined">print</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                                    <p>No hay ventas en este rango de fechas.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('livewire.reports.charts-script')