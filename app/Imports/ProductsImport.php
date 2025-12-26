<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public $created = [];
    public $updated = [];
    public $failed = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'nombre' => 'required',
                'precio_venta' => 'required|numeric',
                'id_categoria' => 'required|exists:categories,id',
                'codigo_barras' => 'nullable',
            ]);

            if ($validator->fails()) {
                $this->failed[] = [
                    'row' => $index + 2,
                    'name' => $row['nombre'] ?? 'Sin nombre',
                    'error' => implode(', ', $validator->errors()->all())
                ];
                continue;
            }

            try {
                $product = null;
                if (!empty($row['codigo_barras'])) {
                    $product = Product::where('barcode', $row['codigo_barras'])->first();
                }

                $data = [
                    'category_id' => $row['id_categoria'],
                    'name' => $row['nombre'],
                    'description' => $row['descripcion'] ?? null,
                    'cost_price' => $row['precio_costo'] ?? 0,
                    'selling_price' => $row['precio_venta'],
                    'stock' => $row['stock'] ?? 0,
                    'min_stock' => $row['stock_minimo'] ?? 5,
                    'is_active' => true,
                ];

                if ($product) {
                    $product->update($data);
                    $this->updated[] = [
                        'name' => $product->name,
                        'barcode' => $product->barcode
                    ];
                } else {
                    $data['barcode'] = $row['codigo_barras'];
                    Product::create($data);
                    $this->created[] = [
                        'name' => $data['name'],
                        'barcode' => $data['barcode']
                    ];
                }

            } catch (\Exception $e) {
                $this->failed[] = [
                    'row' => $index + 2,
                    'name' => $row['nombre'] ?? 'Desconocido',
                    'error' => $e->getMessage()
                ];
            }
        }
    }
}