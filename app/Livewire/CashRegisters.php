<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Livewire\Component;
use Livewire\Attributes\On;

class CashRegisters extends Component
{
    public $register; // Single cash register
    public $showEditModal = false;
    public $name = '';

    public function mount()
    {
        $this->loadRegister();
    }

    #[On('cash-register-updated')]
    public function loadRegister()
    {
        // Load the single cash register (first one)
        $this->register = CashRegister::with('currentSession.user')->first();
        
        // If no register exists, create a default one
        if (!$this->register) {
            $this->register = CashRegister::create([
                'name' => 'Caja Principal',
                'is_active' => true,
                'is_open' => false,
            ]);
        }
    }

    public function edit()
    {
        $this->name = $this->register->name;
        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->register->update(['name' => $this->name]);

        $this->showEditModal = false;
        $this->loadRegister();
        session()->flash('message', 'Caja actualizada exitosamente');
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->reset('name');
    }

    public function render()
    {
        return view('livewire.cash-registers');
    }
}
