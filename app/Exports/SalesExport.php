<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Sale::with(['user', 'client', 'cashRegister'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->get();
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->created_at->format('d/m/Y H:i'),
            $sale->user->name ?? 'Usuario Borrado',
            $sale->client->name ?? 'Consumidor Final',
            $sale->cashRegister->name ?? 'Caja',
            $sale->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta',
            $sale->total,
            $sale->status
        ];
    }

    public function headings(): array
    {
        return [
            'ID Venta', 'Fecha', 'Vendedor', 'Cliente', 'Caja', 'Método Pago', 'Total', 'Estado'
        ];
    }
}