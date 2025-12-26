<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

class SessionDetail extends Component
{
    public $session;
    public $sessionId;
    
    public $totalSales = 0;
    public $cashSales = 0;
    public $cardSales = 0;
    public $salesCount = 0;
    
    public $sales = [];

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        
        // Cargar sesión cerrada con relaciones
        $this->session = CashRegisterSession::with(['cashRegister', 'user', 'closedBy'])
            ->where('id', $this->sessionId)
            ->where('status', 'closed')
            ->firstOrFail();

        // Calcular totales de ventas
        $this->sales = Sale::with('client')
            ->where('cash_register_id', $this->session->cash_register_id)
            ->whereBetween('created_at', [$this->session->created_at, $this->session->closed_at])
            ->latest()
            ->get();

        $this->salesCount = $this->sales->count();
        $this->totalSales = $this->sales->sum('total');
        $this->cashSales = $this->sales->where('payment_method', 'cash')->sum('total');
        $this->cardSales = $this->sales->where('payment_method', 'card')->sum('total');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.session-detail');
    }
}
