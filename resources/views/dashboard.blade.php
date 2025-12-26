<x-layouts.app title="Dashboard - Nexus POS">
    <div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Bienvenido, {{ auth()->user()->name ?? 'Usuario' }}</h2>
                <p class="text-slate-500 dark:text-gray-400 mt-1">Resumen general de las operaciones de hoy.</p>
            </div>
            <div class="flex gap-3">
                 </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            <a href="{{ route('pos') }}" class="group relative col-span-1 sm:col-span-2 row-span-2 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-xl shadow-blue-600/20 transition-all hover:scale-[1.01] hover:shadow-2xl overflow-hidden flex flex-col justify-between min-h-[300px]"
               x-data="salesChart()" x-init="initChart()">
                <div class="absolute right-0 top-0 opacity-10 -mr-16 -mt-16 rounded-full bg-white/30 p-20 blur-2xl"></div>
                
                <div class="relative z-10 flex items-start justify-between">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl inline-flex">
                        <span class="material-symbols-outlined text-4xl">shopping_cart</span>
                    </div>
                    <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                        <span class="size-2 bg-green-400 rounded-full animate-pulse"></span>
                        Caja Abierta
                    </span>
                </div>

                <!-- Sales Chart -->
                <div class="relative z-10 my-4 flex-1">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-white/80">Ventas de Hoy</span>
                            <span class="text-lg font-bold" x-text="'$' + totalSales.toFixed(2)">$0.00</span>
                        </div>
                        <div class="flex-1 min-h-0">
                            <canvas id="salesLineChart" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <h3 class="text-3xl font-bold mb-1">Terminal POS</h3>
                    <p class="text-blue-100 text-lg">Iniciar venta o gestionar caja.</p>
                </div>
                
                <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-4 group-hover:translate-x-0">
                    <span class="material-symbols-outlined text-5xl">arrow_forward</span>
                </div>
            </a>

            <script>
            function salesChart() {
                return {
                    chart: null,
                    totalSales: 0,
                    initChart() {
                        // Datos de ejemplo - reemplazar con datos reales de PHP
                        const salesData = @json($salesToday ?? array_fill(0, 24, 0));
                        this.totalSales = salesData.reduce((a, b) => a + b, 0);
                        
                        const ctx = document.getElementById('salesLineChart');
                        if (!ctx) return;
                        
                        this.chart = new Chart(ctx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', 
                                        '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                                datasets: [{
                                    label: 'Ventas ($)',
                                    data: salesData,
                                    borderColor: 'rgba(255, 255, 255, 0.9)',
                                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                    pointHoverBackgroundColor: 'white',
                                    pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
                                    pointHoverBorderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                        titleColor: '#1e293b',
                                        bodyColor: '#1e293b',
                                        borderColor: 'rgba(59, 130, 246, 0.5)',
                                        borderWidth: 1,
                                        padding: 8,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return '$' + context.parsed.y.toFixed(2);
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        ticks: { 
                                            color: 'rgba(255, 255, 255, 0.7)',
                                            font: { size: 10 },
                                            callback: function(value) {
                                                return '$' + value;
                                            }
                                        },
                                        grid: { 
                                            color: 'rgba(255, 255, 255, 0.1)',
                                            drawBorder: false
                                        },
                                        border: { display: false }
                                    },
                                    x: {
                                        ticks: { 
                                            color: 'rgba(255, 255, 255, 0.7)',
                                            font: { size: 10 },
                                            maxRotation: 0,
                                            autoSkip: true,
                                            maxTicksLimit: 12
                                        },
                                        grid: { display: false },
                                        border: { display: false }
                                    }
                                }
                            }
                        });
                    }
                }
            }
            </script>

            <!-- Cash Register Card -->
            <livewire:cash-register-card />


            <a href="{{ route('products') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">inventory_2</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Productos</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Catálogo y precios.</p>
                </div>
            </a>

            <a href="{{ route('categories') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">category</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Categorías</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Clasificación de productos.</p>
                </div>
            </a>
            <a href="{{ route('inventory.adjustments') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">warehouse</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Inventario</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Stock y ajustes.</p>
                </div>
            </a>

            <a href="{{ route('clients') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">group</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Clientes</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Base de datos.</p>
                </div>
            </a>

            <a href="{{ route('reports') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">bar_chart</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Reportes</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Ingresos y métricas.</p>
                </div>
            </a>

            <a href="{{ route('config') }}" class="group flex flex-col p-6 bg-white dark:bg-[#1A2633] rounded-2xl border border-transparent hover:border-blue-500/30 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="size-12 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-45">
                        <span class="material-symbols-outlined text-2xl">settings</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors">Configuración</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Sistema y empresa.</p>
                </div>
            </a>

        </div>
    </div>
</x-layouts.app>