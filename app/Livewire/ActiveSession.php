<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ActiveSession extends Component
{
    public $session;
    public $sessionId;
    
    public $totalSales = 0;
    public $cashSales = 0;
    public $cardSales = 0;
    public $salesCount = 0;
    public $expectedCash = 0;
    
    public $recentSales = [];

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->loadSessionData();
    }

    public function loadSessionData()
    {
        // Cargar sesión con relaciones
        $this->session = CashRegisterSession::with(['cashRegister', 'user'])
            ->where('id', $this->sessionId)
            ->where('status', 'open')
            ->firstOrFail();

        // Calcular totales de ventas
        $sales = Sale::where('cash_register_id', $this->session->cash_register_id)
            ->whereBetween('created_at', [$this->session->created_at, now()])
            ->get();

        $this->salesCount = $sales->count();
        $this->totalSales = $sales->sum('total');
        $this->cashSales = $sales->where('payment_method', 'cash')->sum('total');
        $this->cardSales = $sales->where('payment_method', 'card')->sum('total');
        
        // Calcular efectivo esperado
        $this->expectedCash = $this->session->opening_amount + $this->cashSales;

        // Últimas 10 ventas
        $this->recentSales = Sale::with('client')
            ->where('cash_register_id', $this->session->cash_register_id)
            ->whereBetween('created_at', [$this->session->created_at, now()])
            ->latest()
            ->take(10)
            ->get();
    }

    public function refresh()
    {
        $this->loadSessionData();
        session()->flash('message', 'Datos actualizados');
    }

    public function closeSessionAsAdmin()
    {
        // Redirigir a la página de cierre normal
        return redirect()->route('cash.close');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.active-session');
    }
}
