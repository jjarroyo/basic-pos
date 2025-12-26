<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model {
    protected $fillable = ['name', 'is_active', 'is_open'];

    public function sessions() { return $this->hasMany(CashRegisterSession::class); }
    public function currentSession() { 
        return $this->hasOne(CashRegisterSession::class)->where('status', 'open')->latest(); 
    }
}
