<?php

namespace App\Livewire;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class SessionHistory extends Component
{
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $selectedCashRegister = '';

    public function mount()
    {
        // Establecer fechas por defecto (último mes)
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->selectedCashRegister = '';
        $this->resetPage();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = CashRegisterSession::with(['cashRegister', 'user', 'closedBy'])
            ->where('status', 'closed');

        // Aplicar filtros
        if ($this->startDate) {
            $query->whereDate('closed_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('closed_at', '<=', $this->endDate);
        }

        if ($this->selectedCashRegister) {
            $query->where('cash_register_id', $this->selectedCashRegister);
        }

        $sessions = $query->latest('closed_at')->paginate(20);
        $cashRegisters = CashRegister::where('is_active', true)->get();

        return view('livewire.session-history', [
            'sessions' => $sessions,
            'cashRegisters' => $cashRegisters,
        ]);
    }
}
