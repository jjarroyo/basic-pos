<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'cash_register_id',
        'client_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax',
        'total',
        'payment_method',
        'cash_received',
        'change',
        'status',
        'synced_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function details() { return $this->hasMany(SaleDetail::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function cashRegister() { return $this->belongsTo(CashRegister::class); }
    public function client() { return $this->belongsTo(Client::class); }
}