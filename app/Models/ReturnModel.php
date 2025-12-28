<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'sale_id',
        'user_id',
        'cash_register_session_id',
        'total_refund',
        'payment_method',
        'reason',
        'notes',
        'status',
    ];

    protected $casts = [
        'total_refund' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function details()
    {
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'reference_id')
            ->where('reference_type', 'return');
    }
}
