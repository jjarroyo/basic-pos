<?php

namespace App\Livewire;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Livewire\Component;

class OpenCashRegister extends Component
{
    public $registers;
    public $selectedRegisterId;
    public $amount = 0;

    public function mount() { 
        $this->registers = CashRegister::where('is_active', true)->where('is_open', false)->get();
    }

    public function openRegister()
    {
        $this->validate([
            'selectedRegisterId' => 'required|exists:cash_registers,id',
            'amount' => 'required|numeric|min:0'
        ]);

        CashRegisterSession::create([
            'cash_register_id' => $this->selectedRegisterId,
            'user_id' => auth()->id(),
            'starting_cash' => $this->amount,
            'opened_at' => now(),
            'status' => 'open'
        ]);
 
        $register = CashRegister::find($this->selectedRegisterId);
        $register->update(['is_open' => true]); 
        session(['cash_register_session_id' => $this->selectedRegisterId]);

        return redirect()->route('pos');
    }

    public function render() { return view('livewire.open-cash-register'); }
}