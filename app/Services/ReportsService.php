<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsService
{
    /**
     * Get sales trend data grouped by period
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $groupBy 'day', 'week', 'month'
     * @return array
     */
    public function getSalesTrend($startDate, $endDate, $groupBy = 'day')
    {
        $query = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        switch ($groupBy) {
            case 'day':
                $data = $query->selectRaw('DATE(created_at) as period, SUM(total) as total, COUNT(*) as count')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;
            
            case 'week':
                // SQLite compatible week grouping
                $data = $query->selectRaw('strftime("%Y-%W", created_at) as period, SUM(total) as total, COUNT(*) as count')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;
            
            case 'month':
                // SQLite compatible month grouping
                $data = $query->selectRaw('strftime("%Y-%m", created_at) as period, SUM(total) as total, COUNT(*) as count')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;
            
            default:
                $data = collect();
        }

        // Fill missing dates with zeros
        return $this->fillMissingPeriods($data, $startDate, $endDate, $groupBy);
    }

    /**
     * Fill missing periods with zero values
     */
    private function fillMissingPeriods($data, $startDate, $endDate, $groupBy)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $filled = [];
        $dataByPeriod = $data->keyBy('period');

        $current = $start->copy();
        while ($current <= $end) {
            $periodKey = match($groupBy) {
                'day' => $current->format('Y-m-d'),
                'week' => $current->format('oW'),
                'month' => $current->format('Y-m'),
                default => $current->format('Y-m-d'),
            };

            $filled[] = [
                'period' => $periodKey,
                'label' => $this->formatPeriodLabel($current, $groupBy),
                'total' => $dataByPeriod->get($periodKey)?->total ?? 0,
                'count' => $dataByPeriod->get($periodKey)?->count ?? 0,
            ];

            match($groupBy) {
                'day' => $current->addDay(),
                'week' => $current->addWeek(),
                'month' => $current->addMonth(),
                default => $current->addDay(),
            };
        }

        return $filled;
    }

    /**
     * Format period label for display
     */
    private function formatPeriodLabel($date, $groupBy)
    {
        return match($groupBy) {
            'day' => $date->format('d M'),
            'week' => 'Sem ' . $date->format('W'),
            'month' => $date->format('M Y'),
            default => $date->format('d M'),
        };
    }

    /**
     * Get seller performance data
     */
    public function getSellerPerformance($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('user_id', DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as total_transactions'))
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('total_sales')
            ->get()
            ->map(function ($sale) {
                return [
                    'seller' => $sale->user->name ?? 'Sin asignar',
                    'total_sales' => $sale->total_sales,
                    'total_transactions' => $sale->total_transactions,
                    'average_ticket' => $sale->total_transactions > 0 ? $sale->total_sales / $sale->total_transactions : 0,
                ];
            });
    }

    /**
     * Get stock levels with alerts
     */
    public function getStockLevels($limit = 20)
    {
        return Product::select('id', 'name', 'stock', 'min_stock')
            ->orderBy('stock', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $status = 'normal';
                if ($product->stock <= ($product->min_stock ?? 5)) {
                    $status = 'critical';
                } elseif ($product->stock <= ($product->min_stock ?? 5) * 2) {
                    $status = 'low';
                }

                return [
                    'product' => $product->name,
                    'stock' => $product->stock,
                    'min_stock' => $product->min_stock ?? 5,
                    'status' => $status,
                ];
            });
    }

    /**
     * Get top selling products
     */
    public function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'products.name',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('SUM(sale_details.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts($threshold = 10)
    {
        return Product::where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'min_stock' => $product->min_stock ?? 5,
                    'recommended_order' => max(($product->min_stock ?? 5) * 3 - $product->stock, 0),
                ];
            });
    }

    /**
     * Get slow moving products (no sales in X days)
     */
    public function getSlowMovingProducts($days = 30, $limit = 20)
    {
        $cutoffDate = Carbon::now()->subDays($days);

        return Product::whereDoesntHave('saleDetails', function ($query) use ($cutoffDate) {
                $query->whereHas('sale', function ($q) use ($cutoffDate) {
                    $q->where('created_at', '>=', $cutoffDate);
                });
            })
            ->where('stock', '>', 0)
            ->select('id', 'name', 'stock', 'price')
            ->orderByDesc('stock')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product->name,
                    'stock' => $product->stock,
                    'inventory_value' => $product->stock * $product->price,
                ];
            });
    }

    /**
     * Get payment methods distribution
     */
    public function getPaymentMethodsDistribution($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('payment_method', DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->payment_method === 'cash' ? 'Efectivo' : 'Tarjeta',
                    'total' => $item->total,
                    'count' => $item->count,
                ];
            });
    }
}
