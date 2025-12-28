<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Sale;
use App\Models\Expense;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CloseCashRegister extends Component
{
    public $session;
    public $sessionId = null; // Optional session ID for admin closing other users' sessions
    public $expectedCash = 0;
    public $expectedCard = 0;
    public $actualCash = '';
    public $difference = 0;
    public $closingNotes = '';
    
    public $totalSales = 0;
    public $cashSales = 0;
    public $cardSales = 0;
    public $salesCount = 0;

    // Expenses
    public $totalExpenses = 0;
    public $expensesCash = 0;
    public $expensesOther = 0;
    public $expensesByCategory = [];
    
    // Profitability
    public $netProfit = 0;

    public function mount($sessionId = null)
    {
        $this->sessionId = $sessionId;
        
        // If sessionId is provided, load that specific session (for admins)
        // Otherwise, load the current user's open session
        if ($this->sessionId) {
            $this->session = CashRegisterSession::with('cashRegister')
                ->where('id', $this->sessionId)
                ->where('status', 'open')
                ->first();
        } else {
            $this->session = CashRegisterSession::with('cashRegister')
                ->where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();
        }

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
 
        // Get expenses for this session
        $expenses = Expense::where('cash_register_session_id', $this->session->id)->get();
        
        $this->totalExpenses = $expenses->sum('amount');
        $this->expensesCash = $expenses->where('payment_method', 'cash')->sum('amount');
        $this->expensesOther = $expenses->whereIn('payment_method', ['card', 'transfer'])->sum('amount');
        
        // Expenses by category
        $categories = [
            'damaged_products' => 'Productos Dañados',
            'services' => 'Servicios',
            'supplies' => 'Suministros',
            'salaries' => 'Nómina',
            'rent' => 'Alquiler',
            'other' => 'Otros',
        ];
        
        $this->expensesByCategory = $expenses->groupBy('category')
            ->map(function ($items, $key) use ($categories) {
                return [
                    'name' => $categories[$key] ?? $key,
                    'total' => $items->sum('amount'),
                ];
            })
            ->values()
            ->toArray();
        
        // Calculate profitability
        $this->netProfit = $this->totalSales - $this->totalExpenses;
 
        // Expected cash should consider expenses in cash
        $this->expectedCash = ($this->session->starting_cash ?? 0) + $this->cashSales - $this->expensesCash;
        
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

        // Dispatch event to notify other components to refresh
        $this->dispatch('cash-register-updated');

        session()->flash('message', '¡Caja cerrada exitosamente!');
        return redirect()->route('dashboard');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.close-cash-register');
    }
}
