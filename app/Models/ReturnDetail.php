<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    protected $fillable = [
        'return_id',
        'sale_detail_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'disposition',
        'disposition_notes',
        'exchange_product_id',
        'exchange_quantity',
        'exchange_unit_price',
        'price_difference',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'exchange_unit_price' => 'decimal:2',
        'price_difference' => 'decimal:2',
    ];

    public function returnModel()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }

    public function saleDetail()
    {
        return $this->belongsTo(SaleDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function exchangeProduct()
    {
        return $this->belongsTo(Product::class, 'exchange_product_id');
    }
}
