<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals-row {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0;
        }
        .totals-label {
            font-weight: bold;
            margin-right: 20px;
            min-width: 120px;
            text-align: right;
        }
        .totals-value {
            min-width: 100px;
            text-align: right;
        }
        .total-final {
            font-size: 1.2em;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Comprobante de Venta</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Venta #:</span>
            <span>{{ $sale->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ $sale->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span>{{ $sale->client->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vendedor:</span>
            <span>{{ $sale->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Método de Pago:</span>
            <span>{{ ucfirst($sale->payment_method) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th style="text-align: center;">Cantidad</th>
                <th style="text-align: right;">Precio</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td style="text-align: center;">{{ $detail->quantity }}</td>
                <td style="text-align: right;">${{ number_format($detail->price, 2) }}</td>
                <td style="text-align: right;">${{ number_format($detail->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span class="totals-label">Subtotal:</span>
            <span class="totals-value">${{ number_format($sale->subtotal, 2) }}</span>
        </div>
        
        @if($sale->discount_amount > 0)
        <div class="totals-row">
            <span class="totals-label">Descuento:</span>
            <span class="totals-value">-${{ number_format($sale->discount_amount, 2) }}</span>
        </div>
        @endif

        @if($sale->tax > 0)
        <div class="totals-row">
            <span class="totals-label">Impuesto:</span>
            <span class="totals-value">${{ number_format($sale->tax, 2) }}</span>
        </div>
        @endif

        <div class="totals-row total-final">
            <span class="totals-label">TOTAL:</span>
            <span class="totals-value">${{ number_format($sale->total, 2) }}</span>
        </div>

        @if($sale->payment_method === 'cash')
        <div class="totals-row" style="margin-top: 10px;">
            <span class="totals-label">Efectivo Recibido:</span>
            <span class="totals-value">${{ number_format($sale->cash_received, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Cambio:</span>
            <span class="totals-value">${{ number_format($sale->change, 2) }}</span>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Gracias por su compra</p>
        <p>Este es un comprobante electrónico generado automáticamente</p>
    </div>
</body>
</html>
