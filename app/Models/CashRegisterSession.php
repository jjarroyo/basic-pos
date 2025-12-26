<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model {
    protected $fillable = ['cash_register_id', 'user_id', 'closed_by_user_id', 'starting_cash', 'closing_cash', 'calculated_cash', 'opened_at', 'closed_at', 'status','expected_cash','expected_card','actual_cash','difference','closing_notes'];
    
    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
    
    public function cashRegister() { return $this->belongsTo(CashRegister::class, 'cash_register_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function closedBy() { return $this->belongsTo(User::class, 'closed_by_user_id'); }
}
