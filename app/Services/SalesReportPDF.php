<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use Carbon\Carbon;

class SalesReportPDF
{
    /**
     * Generate complete sales report PDF
     */
    public function generate($data, $chartImages = [])
    {
        $html = $this->buildHTML($data, $chartImages);
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('letter', 'portrait');
        
        return $pdf;
    }

    /**
     * Build complete HTML for PDF
     */
    private function buildHTML($data, $chartImages)
    {
        $content = '';
        
        // Cover page
        $content .= $this->buildCoverPage($data);
        
        // Executive summary
        $content .= $this->buildExecutiveSummary($data);
        
        // Charts
        if (!empty($chartImages)) {
            $content .= $this->buildChartsSection($chartImages);
        }
        
        // Data tables
        $content .= $this->buildDataTables($data);
        
        // Recommendations
        $content .= $this->buildRecommendations($data);
        
        return $this->wrapInTemplate($content);
    }

    /**
     * Build cover page
     */
    private function buildCoverPage($data)
    {
        $companyName = Setting::get('company_name', 'Nexus POS');
        $logoPath = Setting::get('company_logo');
        
        $startDate = Carbon::parse($data['startDate'])->format('d/m/Y');
        $endDate = Carbon::parse($data['endDate'])->format('d/m/Y');
        
        $logoHtml = '';
        if ($logoPath && file_exists(storage_path('app/public/' . $logoPath))) {
            $logoBase64 = base64_encode(file_get_contents(storage_path('app/public/' . $logoPath)));
            $logoHtml = '<img src="data:image/png;base64,' . $logoBase64 . '" class="logo" alt="Logo">';
        }
        
        return '
        <div class="cover">
            ' . $logoHtml . '
            <h1>Reporte de Ventas</h1>
            <h2>' . $companyName . '</h2>
            <p class="period">Período: ' . $startDate . ' - ' . $endDate . '</p>
            <p class="generated">Generado el ' . Carbon::now()->format('d/m/Y H:i') . '</p>
        </div>
        <div class="page-break"></div>
        ';
    }

    /**
     * Build executive summary
     */
    private function buildExecutiveSummary($data)
    {
        return '
        <div class="section">
            <h2>Resumen Ejecutivo</h2>
            
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Ventas Totales</div>
                    <div class="kpi-value">$' . number_format($data['totalSales'], 2) . '</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-label">Transacciones</div>
                    <div class="kpi-value">' . number_format($data['totalTransactions']) . '</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-label">Ticket Promedio</div>
                    <div class="kpi-value">$' . number_format($data['averageTicket'], 2) . '</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-label">Ventas en Efectivo</div>
                    <div class="kpi-value">$' . number_format($data['cashSales'], 2) . '</div>
                </div>
            </div>
            
            <div class="highlight-box">
                <strong>Highlights del Período:</strong>
                <ul>
                    <li>Se procesaron ' . number_format($data['totalTransactions']) . ' transacciones</li>
                    <li>Ventas en efectivo: $' . number_format($data['cashSales'], 2) . ' (' . ($data['totalSales'] > 0 ? round(($data['cashSales'] / $data['totalSales']) * 100, 1) : 0) . '%)</li>
                    <li>Ventas con tarjeta: $' . number_format($data['cardSales'], 2) . ' (' . ($data['totalSales'] > 0 ? round(($data['cardSales'] / $data['totalSales']) * 100, 1) : 0) . '%)</li>
                </ul>
            </div>
        </div>
        <div class="page-break"></div>
        ';
    }

    /**
     * Build charts section
     */
    private function buildChartsSection($chartImages)
    {
        $html = '';
        
        $chartTitles = [
            'salesChart' => 'Tendencia de Ventas',
            'sellerChart' => 'Rendimiento de Vendedores',
            'stockChart' => 'Niveles de Inventario',
            'topProductsChart' => 'Top 10 Productos Más Vendidos',
            'paymentMethodsChart' => 'Distribución de Métodos de Pago',
        ];
        
        foreach ($chartImages as $key => $imageData) {
            if (!empty($imageData) && isset($chartTitles[$key])) {
                $html .= '
                <div class="chart-section">
                    <h2>' . $chartTitles[$key] . '</h2>
                    <div class="chart-container">
                        <img src="' . $imageData . '" class="chart-image" alt="' . $chartTitles[$key] . '">
                    </div>
                </div>
                <div class="page-break"></div>
                ';
            }
        }
        
        return $html;
    }

    /**
     * Build data tables
     */
    private function buildDataTables($data)
    {
        $html = '<div class="section"><h2>Análisis Detallado</h2>';
        
        // Top products table
        if (!empty($data['topProducts']['labels'])) {
            $html .= '
            <h3>Top Productos Más Vendidos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Ingresos</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($data['topProducts']['labels'] as $index => $label) {
                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($label) . '</td>
                        <td>' . number_format($data['topProducts']['quantities'][$index]) . '</td>
                        <td>$' . number_format($data['topProducts']['revenues'][$index], 2) . '</td>
                    </tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        // Low stock table
        if (!empty($data['lowStock']) && $data['lowStock']->count() > 0) {
            $html .= '
            <h3>Productos con Bajo Stock</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Mínimo</th>
                        <th>Sugerido Ordenar</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($data['lowStock'] as $product) {
                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($product['name']) . '</td>
                        <td class="alert">' . $product['stock'] . '</td>
                        <td>' . $product['min_stock'] . '</td>
                        <td>' . $product['recommended_order'] . '</td>
                    </tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Build recommendations
     */
    private function buildRecommendations($data)
    {
        $recommendations = [];
        
        // Low stock recommendations
        if (!empty($data['lowStock']) && $data['lowStock']->count() > 0) {
            $recommendations[] = '⚠️ <strong>Acción Urgente:</strong> ' . $data['lowStock']->count() . ' productos requieren reabastecimiento inmediato.';
        }
        
        // Slow moving recommendations
        if (!empty($data['slowMoving']) && $data['slowMoving']->count() > 0) {
            $totalValue = $data['slowMoving']->sum('inventory_value');
            $recommendations[] = '📦 <strong>Inventario Estancado:</strong> ' . $data['slowMoving']->count() . ' productos sin movimiento (valor: $' . number_format($totalValue, 0) . '). Considera promociones.';
        }
        
        // Payment method recommendation
        if ($data['totalSales'] > 0) {
            $cashPercentage = ($data['cashSales'] / $data['totalSales']) * 100;
            if ($cashPercentage > 70) {
                $recommendations[] = '💳 <strong>Sugerencia:</strong> Promover pagos con tarjeta para mejor control y seguridad.';
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = '✅ <strong>Excelente:</strong> No se detectaron problemas críticos en este período.';
        }
        
        $html = '
        <div class="section">
            <h2>Recomendaciones</h2>
            <div class="recommendations">
                <ul>';
        
        foreach ($recommendations as $rec) {
            $html .= '<li>' . $rec . '</li>';
        }
        
        $html .= '
                </ul>
            </div>
        </div>';
        
        return $html;
    }

    /**
     * Wrap content in HTML template
     */
    private function wrapInTemplate($content)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Reporte de Ventas</title>
            <style>
                @page { margin: 20mm; }
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 10pt;
                    color: #333;
                    line-height: 1.6;
                }
                .cover {
                    text-align: center;
                    padding-top: 100px;
                }
                .logo {
                    max-width: 150px;
                    max-height: 150px;
                    margin-bottom: 30px;
                }
                h1 { 
                    color: #2563eb; 
                    font-size: 28pt;
                    margin: 20px 0;
                }
                h2 {
                    color: #1e40af;
                    font-size: 18pt;
                    margin: 20px 0 10px 0;
                    border-bottom: 2px solid #2563eb;
                    padding-bottom: 5px;
                }
                h3 {
                    color: #1e40af;
                    font-size: 14pt;
                    margin: 15px 0 10px 0;
                }
                .period {
                    font-size: 14pt;
                    color: #666;
                    margin: 10px 0;
                }
                .generated {
                    font-size: 10pt;
                    color: #999;
                    margin-top: 20px;
                }
                .kpi-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                    margin: 20px 0;
                }
                .kpi-card {
                    border: 2px solid #e5e7eb;
                    padding: 15px;
                    border-radius: 8px;
                    background-color: #f9fafb;
                }
                .kpi-label {
                    font-size: 9pt;
                    color: #666;
                    margin-bottom: 5px;
                }
                .kpi-value {
                    font-size: 20pt;
                    font-weight: bold;
                    color: #2563eb;
                }
                .highlight-box {
                    background-color: #eff6ff;
                    border-left: 4px solid #2563eb;
                    padding: 15px;
                    margin: 20px 0;
                }
                .chart-section {
                    margin: 20px 0;
                }
                .chart-container {
                    text-align: center;
                    margin: 20px 0;
                }
                .chart-image {
                    max-width: 100%;
                    height: auto;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                th, td {
                    border: 1px solid #e5e7eb;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f3f4f6;
                    font-weight: bold;
                    color: #1f2937;
                }
                tr:nth-child(even) {
                    background-color: #f9fafb;
                }
                .alert {
                    color: #dc2626;
                    font-weight: bold;
                }
                .recommendations ul {
                    list-style: none;
                    padding: 0;
                }
                .recommendations li {
                    background-color: #fef3c7;
                    border-left: 4px solid #f59e0b;
                    padding: 10px;
                    margin: 10px 0;
                }
                .page-break {
                    page-break-after: always;
                }
                .section {
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            ' . $content . '
        </body>
        </html>
        ';
    }
}
