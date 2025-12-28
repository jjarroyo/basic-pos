<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\CashRegisterSession;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Expenses extends Component
{
    use WithPagination;

    public $showModal = false;
    public $isEditing = false;
    public $expenseId = null;

    // Filters
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $categoryFilter = '';
    public $sessionFilter = '';

    // Form fields
    public $category = '';
    public $description = '';
    public $amount = '';
    public $payment_method = 'cash';
    public $receipt_number = '';
    public $cash_register_session_id = null;

    // Categories
    public $categories = [
        'damaged_products' => 'Productos Dañados',
        'services' => 'Servicios',
        'supplies' => 'Suministros',
        'salaries' => 'Nómina',
        'rent' => 'Alquiler',
        'other' => 'Otros',
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $expenses = Expense::with(['user', 'cashRegisterSession'])
            ->when($this->search, function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('receipt_number', 'like', "%{$this->search}%");
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->sessionFilter, function ($query) {
                $query->where('cash_register_session_id', $this->sessionFilter);
            })
            ->latest()
            ->paginate(15);

        // Calculate totals
        $totalExpenses = Expense::when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->sum('amount');

        $expensesCash = Expense::where('payment_method', 'cash')
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->sum('amount');

        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$this->categories[$item->category] => $item->total];
            });

        $sessions = CashRegisterSession::where('status', 'open')
            ->orWhere('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('livewire.expenses', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'expensesCash' => $expensesCash,
            'expensesOther' => $totalExpenses - $expensesCash,
            'expensesByCategory' => $expensesByCategory,
            'sessions' => $sessions,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);

        // Prevent editing automatic expenses
        if ($expense->reference_type === 'return') {
            session()->flash('error', 'No se pueden editar gastos automáticos generados por devoluciones.');
            return;
        }

        $this->expenseId = $expense->id;
        $this->category = $expense->category;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->payment_method = $expense->payment_method;
        $this->receipt_number = $expense->receipt_number;
        $this->cash_register_session_id = $expense->cash_register_session_id;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'category' => 'required|in:damaged_products,services,supplies,salaries,rent,other',
            'description' => 'required|min:3',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,transfer',
            'receipt_number' => 'nullable|string',
            'cash_register_session_id' => 'nullable|exists:cash_register_sessions,id',
        ], [
            'category.required' => 'Selecciona una categoría',
            'description.required' => 'Ingresa una descripción',
            'description.min' => 'La descripción debe tener al menos 3 caracteres',
            'amount.required' => 'Ingresa el monto',
            'amount.min' => 'El monto debe ser mayor a 0',
            'payment_method.required' => 'Selecciona un método de pago',
        ]);

        if ($this->isEditing) {
            $expense = Expense::findOrFail($this->expenseId);
            
            // If changing amount or payment method, adjust cash register
            if ($expense->payment_method === 'cash' && $expense->cash_register_session_id) {
                // Return old amount to session
                $session = $expense->cashRegisterSession;
                $session->increment('calculated_cash', $expense->amount);
            }

            $expense->update([
                'category' => $this->category,
                'description' => $this->description,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'receipt_number' => $this->receipt_number,
                'cash_register_session_id' => $this->cash_register_session_id,
            ]);

            // Deduct new amount if cash
            if ($this->payment_method === 'cash' && $this->cash_register_session_id) {
                $session = CashRegisterSession::find($this->cash_register_session_id);
                $session->decrement('calculated_cash', $this->amount);
            }

            session()->flash('success', 'Gasto actualizado correctamente.');
        } else {
            $expense = Expense::create([
                'user_id' => Auth::id(),
                'category' => $this->category,
                'description' => $this->description,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'receipt_number' => $this->receipt_number,
                'cash_register_session_id' => $this->cash_register_session_id,
                'reference_type' => 'manual',
            ]);

            // Update cash register balance if payment is cash
            if ($this->payment_method === 'cash' && $this->cash_register_session_id) {
                $session = CashRegisterSession::find($this->cash_register_session_id);
                $session->decrement('calculated_cash', $this->amount);
            }

            session()->flash('success', 'Gasto registrado correctamente.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);

        // Prevent deleting automatic expenses
        if ($expense->reference_type === 'return') {
            session()->flash('error', 'No se pueden eliminar gastos automáticos generados por devoluciones.');
            return;
        }

        // If cash expense, return amount to session
        if ($expense->payment_method === 'cash' && $expense->cash_register_session_id) {
            $session = $expense->cashRegisterSession;
            if ($session && $session->status === 'open') {
                $session->increment('calculated_cash', $expense->amount);
            }
        }

        $expense->delete();
        session()->flash('success', 'Gasto eliminado correctamente.');
    }

    protected function resetForm()
    {
        $this->expenseId = null;
        $this->category = '';
        $this->description = '';
        $this->amount = '';
        $this->payment_method = 'cash';
        $this->receipt_number = '';
        $this->cash_register_session_id = null;
        $this->resetValidation();
    }
}
