<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Declare charts globally
    let salesChart = null;
    let sellerChart = null;
    let stockChart = null;
    let topProductsChart = null;
    let paymentMethodsChart = null;

    document.addEventListener('livewire:initialized', () => {

        // Sales Trend Chart
        function initSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const chartData = @json($salesChartData ?? []);
            
            // Validate data
            if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                console.log('No sales data available for chart');
                return;
            }
            
            if (salesChart) salesChart.destroy();

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: chartData.data,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (ctx) => 'Ventas: $' + ctx.parsed.y.toLocaleString('es-CO', {minimumFractionDigits: 2}),
                                afterLabel: (ctx) => 'Transacciones: ' + chartData.counts[ctx.dataIndex]
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                callback: (val) => '$' + val.toLocaleString('es-CO'),
                                color: 'rgba(148, 163, 184, 0.8)'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: 'rgba(148, 163, 184, 0.8)', maxRotation: 45 }
                        }
                    }
                }
            });
        }

        // Seller Performance Chart
        function initSellerChart() {
            const ctx = document.getElementById('sellerChart');
            if (!ctx) return;

            const chartData = @json($sellerChartData ?? []);
            
            // Validate data
            if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                console.log('No seller data available for chart');
                return;
            }
            
            if (sellerChart) sellerChart.destroy();

            // Generate gradient colors for each bar
            const colors = chartData.labels.map((_, index) => {
                const hue = (index * 360 / chartData.labels.length);
                return `hsl(${hue}, 70%, 60%)`;
            });

            sellerChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Ventas Totales ($)',
                        data: chartData.sales,
                        backgroundColor: colors.map(c => c.replace('60%', '60%') + '80'),
                        borderColor: colors,
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (ctx) => 'Ventas: $' + ctx.parsed.x.toLocaleString('es-CO', {minimumFractionDigits: 2}),
                                afterLabel: (ctx) => [
                                    'Transacciones: ' + chartData.transactions[ctx.dataIndex],
                                    'Ticket Promedio: $' + chartData.averages[ctx.dataIndex].toLocaleString('es-CO', {minimumFractionDigits: 2})
                                ]
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                callback: (val) => '$' + val.toLocaleString('es-CO'),
                                color: 'rgba(148, 163, 184, 0.8)'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { 
                                color: 'rgba(148, 163, 184, 0.8)',
                                font: { weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }

        // Stock Levels Chart
        function initStockChart() {
            const ctx = document.getElementById('stockChart');
            if (!ctx) return;

            const chartData = @json($stockChartData ?? []);
            
            // Validate data
            if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                console.log('No stock data available for chart');
                return;
            }
            
            if (stockChart) stockChart.destroy();

            // Color-code bars based on status
            const colors = chartData.status.map(status => {
                switch(status) {
                    case 'critical': return 'rgba(239, 68, 68, 0.8)'; // Red
                    case 'low': return 'rgba(251, 191, 36, 0.8)'; // Yellow
                    default: return 'rgba(34, 197, 94, 0.8)'; // Green
                }
            });

            const borderColors = chartData.status.map(status => {
                switch(status) {
                    case 'critical': return 'rgb(239, 68, 68)';
                    case 'low': return 'rgb(251, 191, 36)';
                    default: return 'rgb(34, 197, 94)';
                }
            });

            stockChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Stock Actual',
                        data: chartData.stock,
                        backgroundColor: colors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (ctx) => 'Stock: ' + ctx.parsed.y + ' unidades',
                                afterLabel: (ctx) => [
                                    'Mínimo: ' + chartData.minStock[ctx.dataIndex],
                                    'Estado: ' + (chartData.status[ctx.dataIndex] === 'critical' ? 'Crítico' : 
                                                  chartData.status[ctx.dataIndex] === 'low' ? 'Bajo' : 'Normal')
                                ]
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                callback: (val) => val + ' un.',
                                color: 'rgba(148, 163, 184, 0.8)'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: 'rgba(148, 163, 184, 0.8)',
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Top Products Chart
        function initTopProductsChart() {
            const ctx = document.getElementById('topProductsChart');
            if (!ctx) return;

            const chartData = @json($topProductsChartData ?? []);
            
            // Validate data
            if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                console.log('No top products data available for chart');
                return;
            }
            
            if (topProductsChart) topProductsChart.destroy();

            // Generate gradient colors
            const colors = chartData.labels.map((_, index) => {
                const hue = 200 + (index * 15); // Blue to purple gradient
                return `hsla(${hue}, 70%, 60%, 0.8)`;
            });

            const borderColors = colors.map(c => c.replace('0.8)', '1)'));

            topProductsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: chartData.quantities,
                        backgroundColor: colors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (ctx) => 'Cantidad: ' + ctx.parsed.x + ' unidades',
                                afterLabel: (ctx) => 'Ingresos: $' + chartData.revenues[ctx.dataIndex].toLocaleString('es-CO', {minimumFractionDigits: 2})
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                callback: (val) => val + ' un.',
                                color: 'rgba(148, 163, 184, 0.8)'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { 
                                color: 'rgba(148, 163, 184, 0.8)',
                                font: { weight: 'bold', size: 11 }
                            }
                        }
                    }
                }
            });
        }

        // Payment Methods Chart
        function initPaymentMethodsChart() {
            const ctx = document.getElementById('paymentMethodsChart');
            if (!ctx) return;

            const chartData = @json($paymentMethodsChartData ?? []);
            
            // Validate data
            if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                console.log('No payment methods data available for chart');
                return;
            }
            
            if (paymentMethodsChart) paymentMethodsChart.destroy();

            // Calculate total and percentages
            const total = chartData.totals.reduce((a, b) => a + b, 0);
            const percentages = chartData.totals.map(val => ((val / total) * 100).toFixed(1));

            // Colors for payment methods
            const colors = [
                'rgba(34, 197, 94, 0.8)',  // Green for cash
                'rgba(59, 130, 246, 0.8)',  // Blue for card
            ];

            const borderColors = [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
            ];

            paymentMethodsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.totals,
                        backgroundColor: colors,
                        borderColor: borderColors,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(148, 163, 184, 0.8)',
                                font: { size: 12, weight: 'bold' },
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (ctx) => {
                                    const label = ctx.label || '';
                                    const value = '$' + ctx.parsed.toLocaleString('es-CO', {minimumFractionDigits: 2});
                                    const percent = percentages[ctx.dataIndex] + '%';
                                    return [label + ': ' + value, 'Porcentaje: ' + percent, 'Transacciones: ' + chartData.counts[ctx.dataIndex]];
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize all charts
        initSalesChart();
        initSellerChart();
        initStockChart();
        initTopProductsChart();
        initPaymentMethodsChart();

        // Reinitialize on Livewire updates
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                initSalesChart();
                initSellerChart();
                initStockChart();
                initTopProductsChart();
                initPaymentMethodsChart();
            }, 100);
        });
    });

    // Function to capture charts and export to PDF
    window.exportToPDF = function() {
        // Capture all charts as base64 images
        const charts = {
            salesChart: salesChart?.toBase64Image() || null,
            sellerChart: sellerChart?.toBase64Image() || null,
            stockChart: stockChart?.toBase64Image() || null,
            topProductsChart: topProductsChart?.toBase64Image() || null,
            paymentMethodsChart: paymentMethodsChart?.toBase64Image() || null,
        };
        
        // Send to Livewire and trigger PDF generation
        @this.set('chartImages', charts).then(() => {
            @this.call('exportPDF');
        });
    };
</script>
