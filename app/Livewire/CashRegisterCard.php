<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Livewire\Component;

class CashRegisterCard extends Component
{
    public $cashRegisters;
    public $activeSessions = 0;
    public $totalCash = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->cashRegisters = CashRegister::with(['currentSession.user'])
            ->where('is_active', true)
            ->get();

        $this->activeSessions = $this->cashRegisters->where('is_open', true)->count();
        
        // Calculate total cash from all open sessions
        $this->totalCash = $this->cashRegisters->sum(function ($register) {
            return $register->currentSession?->starting_cash ?? 0;
        });
    }

    public function render()
    {
        return view('livewire.cash-register-card');
    }
}
