<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Services\ReportsService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Component
{
    use WithPagination;

    public $dateRange = 'today';
    public $startDate;
    public $endDate;
    public $groupBy = 'day';
    
    public $totalSales = 0;
    public $totalTransactions = 0;
    public $averageTicket = 0;
    public $cashSales = 0;
    public $cardSales = 0;

    public $chartImages = [];

    public function mount()
    {
        $this->setDates();
    }

    public function updatedDateRange()
    {
        $this->setDates();
        $this->resetPage();
    }

    public function setDates()
    {
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    public function render()
    {
        $query = Sale::with(['user', 'client'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

        $metricsQuery = clone $query;
        $stats = $metricsQuery->selectRaw('
            count(*) as count, 
            sum(total) as total,
            sum(case when payment_method = "cash" then total else 0 end) as cash_total,
            sum(case when payment_method = "card" then total else 0 end) as card_total
        ')->first();

        $this->totalTransactions = $stats->count ?? 0;
        $this->totalSales = $stats->total ?? 0;
        $this->cashSales = $stats->cash_total ?? 0;
        $this->cardSales = $stats->card_total ?? 0;
        $this->averageTicket = $this->totalTransactions > 0 ? $this->totalSales / $this->totalTransactions : 0;

        $sales = $query->latest()->paginate(10);

        // Get chart data
        $salesChartData = $this->getSalesChartData();
        $sellerChartData = $this->getSellerChartData();
        $stockChartData = $this->getStockChartData();
        $topProductsChartData = $this->getTopProductsChartData();
        $paymentMethodsChartData = $this->getPaymentMethodsChartData();
        
        // Get alert data
        $lowStockProducts = $this->getLowStockProducts();
        $slowMovingProducts = $this->getSlowMovingProducts();

        return view('livewire.reports', [
            'sales' => $sales,
            'salesChartData' => $salesChartData,
            'sellerChartData' => $sellerChartData,
            'stockChartData' => $stockChartData,
            'topProductsChartData' => $topProductsChartData,
            'paymentMethodsChartData' => $paymentMethodsChartData,
            'lowStockProducts' => $lowStockProducts,
            'slowMovingProducts' => $slowMovingProducts,
        ]);
    }

    public function getSalesChartData()
    {
        $reportsService = new ReportsService();
        $data = $reportsService->getSalesTrend($this->startDate, $this->endDate, $this->groupBy);
        
        return [
            'labels' => collect($data)->pluck('label')->toArray(),
            'data' => collect($data)->pluck('total')->toArray(),
            'counts' => collect($data)->pluck('count')->toArray(),
        ];
    }

    public function getSellerChartData()
    {
        $reportsService = new ReportsService();
        $data = $reportsService->getSellerPerformance($this->startDate, $this->endDate);
        
        return [
            'labels' => $data->pluck('seller')->toArray(),
            'sales' => $data->pluck('total_sales')->toArray(),
            'transactions' => $data->pluck('total_transactions')->toArray(),
            'averages' => $data->pluck('average_ticket')->toArray(),
        ];
    }

    public function getStockChartData()
    {
        $reportsService = new ReportsService();
        $data = $reportsService->getStockLevels(15); // Top 15 products by lowest stock
        
        return [
            'labels' => $data->pluck('product')->toArray(),
            'stock' => $data->pluck('stock')->toArray(),
            'minStock' => $data->pluck('min_stock')->toArray(),
            'status' => $data->pluck('status')->toArray(),
        ];
    }

    public function getTopProductsChartData()
    {
        $reportsService = new ReportsService();
        $data = $reportsService->getTopProducts($this->startDate, $this->endDate, 10);
        
        return [
            'labels' => $data->pluck('name')->toArray(),
            'quantities' => $data->pluck('total_quantity')->toArray(),
            'revenues' => $data->pluck('total_revenue')->toArray(),
        ];
    }

    public function getPaymentMethodsChartData()
    {
        $reportsService = new ReportsService();
        $data = $reportsService->getPaymentMethodsDistribution($this->startDate, $this->endDate);
        
        return [
            'labels' => $data->pluck('method')->toArray(),
            'totals' => $data->pluck('total')->toArray(),
            'counts' => $data->pluck('count')->toArray(),
        ];
    }

    public function getLowStockProducts()
    {
        $reportsService = new ReportsService();
        return $reportsService->getLowStockProducts(10);
    }

    public function getSlowMovingProducts()
    {
        $reportsService = new ReportsService();
        return $reportsService->getSlowMovingProducts(30, 10);
    }

    public function exportExcel()
    {
        return Excel::download(new SalesExport($this->startDate, $this->endDate), 'reporte_ventas.xlsx');
    }

    public function exportPDF()
    {
        // Validate data
        if ($this->totalSales == 0) {
            session()->flash('error', 'No hay datos para exportar en este período');
            return;
        }

        // Prepare data
        $data = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalSales' => $this->totalSales,
            'totalTransactions' => $this->totalTransactions,
            'averageTicket' => $this->averageTicket,
            'cashSales' => $this->cashSales,
            'cardSales' => $this->cardSales,
            'topProducts' => $this->getTopProductsChartData(),
            'lowStock' => $this->getLowStockProducts(),
            'slowMoving' => $this->getSlowMovingProducts(),
        ];

        // Generate PDF
        $pdfService = new \App\Services\SalesReportPDF();
        $pdf = $pdfService->generate($data, $this->chartImages);

        // Download
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'reporte_ventas_' . date('Y-m-d_His') . '.pdf');
    }
}