<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolución #{{ $return->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos específicos para impresión térmica */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
            }
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            line-height: 1.2;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div class="text-center mb-2">
        <h2 class="text-lg font-bold uppercase">{{ $company['name'] }}</h2>
        <p>NIT: {{ $company['nit'] }}</p>
        <p>{{ $company['address'] }}</p>
        <p>Tel: {{ $company['phone'] }}</p>
    </div>

    <div class="divider"></div>
    
    <div class="text-center mb-2">
        <h3 class="text-md font-bold uppercase">DEVOLUCIÓN</h3>
    </div>

    <div class="divider"></div>
    
    <div>
        <div class="flex justify-between">
            <span>Devolución:</span>
            <span class="font-bold">#{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Venta Original:</span>
            <span>#{{ str_pad($return->sale_id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Fecha:</span>
            <span>{{ $return->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Usuario:</span>
            <span>{{ $return->user->name }}</span>
        </div>
        <div class="flex justify-between">
            <span>Cliente:</span>
            <span>{{ Str::limit($return->sale->client->name ?? 'Consumidor', 15) }}</span>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <table class="w-full text-left">
        <thead>
            <tr class="uppercase text-xs">
                <th class="py-1">Cant.</th>
                <th class="py-1">Descrip.</th>
                <th class="text-right py-1">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->details as $item)
            <tr>
                <td class="align-top py-1 w-8">{{ $item->quantity }}</td>
                <td class="align-top py-1">
                    {{ $item->product->name }}
                    <br>
                    <span class="text-[10px] text-gray-600">${{ number_format($item->unit_price, 0) }} c/u</span>
                    <br>
                    <span class="text-[9px] uppercase">
                        {{ match($item->disposition) {
                            'return_to_stock' => '[Retornado]',
                            'exchange' => '[Cambio]',
                            'damaged_with_expense' => '[Dañado-Gasto]',
                            'damaged_no_expense' => '[Dañado]',
                        } }}
                    </span>
                    @if($item->disposition === 'exchange' && $item->exchangeProduct)
                        <br>
                        <span class="text-[9px]">→ {{ $item->exchangeProduct->name }}</span>
                        <br>
                        <span class="text-[9px]">({{ $item->exchange_quantity }}x ${{ number_format($item->exchange_unit_price, 0) }})</span>
                    @endif
                </td>
                <td class="align-top text-right py-1 font-bold">
                    ${{ number_format($item->subtotal, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>
    
    <div class="space-y-1">
        <div class="flex justify-between text-lg font-bold mt-2">
            <span>TOTAL {{ $return->total_refund > 0 ? 'REEMBOLSADO' : 'COBRADO' }}:</span>
            <span>${{ number_format(abs($return->total_refund), 0) }}</span>
        </div>
    </div>

    <div class="divider"></div>
    
    <div class="space-y-1 text-xs">
        <div class="flex justify-between">
            <span>Método:</span>
            <span>{{ $return->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta' }}</span>
        </div>
    </div>

    <div class="divider"></div>
    
    <div class="text-xs">
        <div class="font-bold mb-1">MOTIVO:</div>
        <div>{{ $return->reason }}</div>
        @if($return->notes)
            <div class="mt-2">
                <div class="font-bold">NOTAS:</div>
                <div>{{ $return->notes }}</div>
            </div>
        @endif
    </div>

    <div class="mt-6 text-center text-xs">
        <p class="mb-2">Gracias por su preferencia</p>
        <p class="text-[10px] text-gray-500">Software: Nexus POS</p>
        
        <div class="mt-2 text-center opacity-80">
            ||| |||| || |||||| ||| || ||||
            <br>
            DEV-{{ str_pad($return->id, 10, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
        
        window.onafterprint = function() {
            // window.close();
        };
    </script>
</body>
</html>
