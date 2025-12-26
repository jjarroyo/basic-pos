<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CloseCashRegister extends Component
{
    public $session;
    public $expectedCash = 0;
    public $expectedCard = 0;
    public $actualCash = '';
    public $difference = 0;
    public $closingNotes = '';
    
    public $totalSales = 0;
    public $cashSales = 0;
    public $cardSales = 0;
    public $salesCount = 0;

    public function mount()
    {
        // Obtener la sesión abierta del usuario con la relación cashRegister
        $this->session = CashRegisterSession::with('cashRegister')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$this->session) {
            session()->flash('error', 'No hay una caja abierta para cerrar.');
            return redirect()->route('dashboard');
        }
 
        $sales = Sale::where('cash_register_id', $this->session->cash_register_id)
            ->whereBetween('created_at', [$this->session->created_at, now()])
            ->get();

        $this->salesCount = $sales->count();
        $this->totalSales = $sales->sum('total');
        $this->cashSales = $sales->where('payment_method', 'cash')->sum('total');
        $this->cardSales = $sales->where('payment_method', 'card')->sum('total');
 
        $this->expectedCash = ($this->session->starting_cash ?? 0) + $this->cashSales;
        
        $this->expectedCard = $this->cardSales;
    }

    public function updatedActualCash()
    {
        if ($this->actualCash !== '') {
            $this->difference = $this->actualCash - $this->expectedCash;
        } else {
            $this->difference = 0;
        }
    }

    public function closeSession()
    {
        $this->validate([
            'actualCash' => 'required|numeric|min:0',
        ], [
            'actualCash.required' => 'Debes ingresar el efectivo contado',
            'actualCash.numeric' => 'El efectivo debe ser un número válido',
            'actualCash.min' => 'El efectivo no puede ser negativo',
        ]);

        // Recalcular totales antes de guardar
        $sales = Sale::where('cash_register_id', $this->session->cash_register_id)
            ->whereBetween('created_at', [$this->session->created_at, now()])
            ->get();

        $totalSales = $sales->sum('total');
        $cashSales = $sales->where('payment_method', 'cash')->sum('total');
        $cardSales = $sales->where('payment_method', 'card')->sum('total');
        
        $expectedCash = ($this->session->starting_cash ?? 0) + $cashSales;
        $expectedCard = $cardSales;
        $difference = $this->actualCash - $expectedCash;

        $this->session->update([
            'status' => 'closed',
            'closing_amount' => $totalSales,
            'expected_cash' => $expectedCash,
            'expected_card' => $expectedCard,
            'actual_cash' => $this->actualCash,
            'difference' => $difference,
            'closing_notes' => $this->closingNotes,
            'closed_by_user_id' => auth()->id(),
            'closed_at' => now(),
        ]);

        $this->session->cashRegister->update([
            'is_open' => false,
        ]);

        session()->flash('message', '¡Caja cerrada exitosamente!');
        return redirect()->route('dashboard');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.close-cash-register');
    }
}
