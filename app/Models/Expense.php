<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'cash_register_session_id',
        'user_id',
        'category',
        'description',
        'amount',
        'reference_type',
        'reference_id',
        'payment_method',
        'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship for reference
    public function reference()
    {
        return $this->morphTo();
    }
}
