<script>
    document.addEventListener('livewire:initialized', () => {
        let sellerChart = null;

        function initSellerChart() {
            const ctx = document.getElementById('sellerChart');
            if (!ctx) return;

            const chartData = @json($sellerChartData ?? ['labels' => [], 'sales' => [], 'transactions' => [], 'averages' => []]);
            
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

        initSellerChart();
        Livewire.hook('morph.updated', () => setTimeout(initSellerChart, 100));
    });
</script>
