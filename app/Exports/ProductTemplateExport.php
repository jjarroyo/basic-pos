<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'nombre',
            'codigo_barras',
            'descripcion',
            'precio_costo',
            'precio_venta',
            'stock',
            'stock_minimo',
            'id_categoria',
        ];
    }
}