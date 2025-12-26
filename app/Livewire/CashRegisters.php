<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Livewire\Component;

class CashRegisters extends Component
{
    public $registers;
    public $showModal = false;
    public $isEditing = false;
    public $editingId = null;
    public $name = '';

    public function mount()
    {
        $this->loadRegisters();
    }

    public function loadRegisters()
    {
        $this->registers = CashRegister::with('currentSession.user')->get();
    }

    public function create()
    {
        $this->reset(['name', 'isEditing', 'editingId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $register = CashRegister::findOrFail($id);
        $this->editingId = $id;
        $this->name = $register->name;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($this->isEditing) {
            $register = CashRegister::findOrFail($this->editingId);
            $register->update(['name' => $this->name]);
        } else {
            CashRegister::create([
                'name' => $this->name,
                'is_active' => true,
                'is_open' => false,
            ]);
        }

        $this->showModal = false;
        $this->loadRegisters();
        $this->reset(['name', 'isEditing', 'editingId']);
    }

    public function delete($id)
    {
        $register = CashRegister::findOrFail($id);
        
        // Check if register has an open session
        if ($register->is_open) {
            session()->flash('error', 'No se puede eliminar una caja con sesión abierta.');
            return;
        }

        $register->delete();
        $this->loadRegisters();
    }

    public function render()
    {
        return view('livewire.cash-registers');
    }
}
