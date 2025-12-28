<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $sale->id }}</title>
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
            font-family: 'Courier New', Courier, monospace; /* Fuente monoespaciada tipo ticket */
            background-color: #fff;
            color: #000;
            width: 80mm; /* Ancho estándar térmica */
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
        
        /* Ocultar barra de scroll */
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body> <div class="text-center mb-2">
        <h2 class="text-lg font-bold uppercase">{{ $company['name'] }}</h2>
        <p>NIT: {{ $company['nit'] }}</p>
        <p>{{ $company['address'] }}</p>
        <p>Tel: {{ $company['phone'] }}</p>
    </div>

    <div class="divider"></div>
    <div>
        <div class="flex justify-between">
            <span>Ticket:</span>
            <span class="font-bold">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Fecha:</span>
            <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Cajero:</span>
            <span>{{ $sale->user->name }}</span>
        </div>
        <div class="flex justify-between">
            <span>Cliente:</span>
            <span>{{ Str::limit($sale->client->name ?? 'Consumidor', 15) }}</span>
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
            @foreach($sale->details as $item)
            <tr>
                <td class="align-top py-1 w-8">{{ $item->quantity }}</td>
                <td class="align-top py-1">
                    {{ $item->product->name }}
                    <br>
                    <span class="text-[10px] text-gray-600">${{ number_format($item->price, 0) }} c/u</span>
                </td>
                <td class="align-top text-right py-1 font-bold">
                    ${{ number_format($item->total, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>
    <div class="space-y-1">
        <div class="flex justify-between text-xs">
            <span>Subtotal:</span>
            <span>${{ number_format($sale->subtotal, 0) }}</span>
        </div>
        <div class="flex justify-between text-xs">
            <span>Impuestos:</span>
            <span>${{ number_format($sale->tax, 0) }}</span>
        </div>
        <div class="flex justify-between text-lg font-bold mt-2">
            <span>TOTAL:</span>
            <span>${{ number_format($sale->total, 0) }}</span>
        </div>
    </div>

    <div class="divider"></div>
    <div class="space-y-1 text-xs">
        <div class="flex justify-between">
            <span>Pago ({{ $sale->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta' }}):</span>
            <span>${{ number_format($sale->cash_received ?? $sale->total, 0) }}</span>
        </div>
        @if($sale->payment_method == 'cash')
        <div class="flex justify-between font-bold">
            <span>Cambio:</span>
            <span>${{ number_format($sale->change, 0) }}</span>
        </div>
        @endif
    </div>

    <div class="mt-6 text-center text-xs">
        <p class="mb-2">{{ $company['footer'] }}</p>
        <p class="text-[10px] text-gray-500">Software: Nexus POS</p>
        
        <div class="mt-2 text-center opacity-80">
            ||| |||| || |||||| ||| || ||||
            <br>
            {{ str_pad($sale->id, 10, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <script>
        @if($openDrawer && $drawerCommand)
        // function to open cash drawer
        function openCashDrawer() {
            try {
                // convert the command string to actual bytes
                const command = {!! json_encode($drawerCommand) !!};
                
                // create a hidden iframe to send the command
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                
                // write the ESC/POS command to the iframe
                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write('<pre>' + command + '</pre>');
                doc.close();
                
                // print the command (this sends it to the printer)
                iframe.contentWindow.print();
                
                // remove the iframe after a short delay
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 100);
                
                console.log('Cash drawer command sent:', command);
            } catch (error) {
                console.error('Error opening cash drawer:', error);
            }
        }
        
        // open drawer before printing the ticket
        window.addEventListener('load', function() {
            openCashDrawer();
            // small delay before printing ticket to ensure drawer command is sent first
            setTimeout(() => {
                window.print();
            }, 200);
        });
        @else
        // just print normally if drawer is not enabled
        window.addEventListener('load', function() {
            window.print();
        });
        @endif
        
        // close window after printing (optional)
        window.onafterprint = function() {
            // window.close();
        };
    </script>
</body>
</html>