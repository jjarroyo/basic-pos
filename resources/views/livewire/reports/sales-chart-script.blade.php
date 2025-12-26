<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let salesChart = null;

        function initSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const chartData = @json($salesChartData ?? ['labels' => [], 'data' => [], 'counts' => []]);
            
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

        initSalesChart();
        Livewire.hook('morph.updated', () => setTimeout(initSalesChart, 100));
    });
</script>
