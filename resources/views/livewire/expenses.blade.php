<div class="flex flex-col h-full bg-slate-50 dark:bg-[#101922]">
    
    {{-- Header --}}
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1A2633] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Control de Gastos</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Gestiona los egresos del negocio</p>
            </div>
        </div>
        
        <button wire:click="create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">add</span>
            Registrar Gasto
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8">
        
        {{-- Metrics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold opacity-90">Total Gastos</span>
                    <span class="material-symbols-outlined text-3xl opacity-80">payments</span>
                </div>
                <div class="text-3xl font-bold">${{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold opacity-90">Gastos en Efectivo</span>
                    <span class="material-symbols-outlined text-3xl opacity-80">local_atm</span>
                </div>
                <div class="text-3xl font-bold">${{ number_format($expensesCash, 0, ',', '.') }}</div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold opacity-90">Otros Métodos</span>
                    <span class="material-symbols-outlined text-3xl opacity-80">credit_card</span>
                </div>
                <div class="text-3xl font-bold">${{ number_format($expensesOther, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Category Breakdown --}}
        @if($expensesByCategory->isNotEmpty())
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Gastos por Categoría</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($expensesByCategory as $cat => $total)
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($total, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $cat }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar..." 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
            </div>

            <input wire:model.live="dateFrom" type="date" class="px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
            
            <input wire:model.live="dateTo" type="date" class="px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">

            <select wire:model.live="categoryFilter" class="px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                <option value="">Todas las categorías</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="sessionFilter" class="px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                <option value="">Todas las sesiones</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">Sesión #{{ $session->id }} - {{ $session->created_at->format('d/m/Y') }}</option>
                @endforeach
            </select>
        </div>

        {{-- Expenses Table --}}
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-[#202e3d] text-slate-500 dark:text-slate-400 font-semibold text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4">Descripción</th>
                        <th class="px-6 py-4">Monto</th>
                        <th class="px-6 py-4">Método</th>
                        <th class="px-6 py-4">Recibo</th>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#253241] transition-colors group">
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                            <div>{{ $expense->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $expense->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-md text-xs font-bold text-white {{ 
                                match($expense->category) {
                                    'damaged_products' => 'bg-red-500',
                                    'services' => 'bg-blue-500',
                                    'supplies' => 'bg-green-500',
                                    'salaries' => 'bg-purple-500',
                                    'rent' => 'bg-orange-500',
                                    'other' => 'bg-gray-500',
                                } 
                            }}">
                                {{ $categories[$expense->category] }}
                            </span>
                            @if($expense->reference_type === 'return')
                                <span class="ml-1 text-xs text-slate-400">(Auto)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                            {{ $expense->description }}
                            @if($expense->cashRegisterSession)
                                <div class="text-xs text-slate-500">Sesión #{{ $expense->cash_register_session_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-red-600 dark:text-red-400">
                            ${{ number_format($expense->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-md text-xs font-semibold {{ 
                                match($expense->payment_method) {
                                    'cash' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'card' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'transfer' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                } 
                            }}">
                                {{ match($expense->payment_method) {
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                    'transfer' => 'Transferencia',
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-mono text-sm">
                            {{ $expense->receipt_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                            {{ $expense->user->name }}
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($expense->reference_type !== 'return')
                                <button wire:click="edit({{ $expense->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button wire:confirm="¿Eliminar este gasto?" wire:click="delete({{ $expense->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            @else
                                <span class="text-xs text-slate-400 italic">Automático</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">receipt_long</span>
                            <p>No hay gastos registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-[#1A2633] z-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $isEditing ? 'Editar Gasto' : 'Registrar Gasto' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Categoría *</label>
                            <select wire:model="category" class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">Seleccionar...</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Monto *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">$</span>
                                <input wire:model="amount" type="number" step="0.01" class="w-full pl-8 pr-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Descripción *</label>
                        <textarea wire:model="description" rows="3" class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Describe el gasto..."></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Método de Pago *</label>
                            <select wire:model="payment_method" class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="cash">Efectivo</option>
                                <option value="card">Tarjeta</option>
                                <option value="transfer">Transferencia</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Número de Recibo</label>
                            <input wire:model="receipt_number" type="text" class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Opcional">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Sesión de Caja (Opcional)</label>
                        <select wire:model="cash_register_session_id" class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Sin vincular a sesión</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">
                                    Sesión #{{ $session->id }} - {{ $session->created_at->format('d/m/Y H:i') }} 
                                    ({{ $session->status === 'open' ? 'Abierta' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Si el pago es en efectivo y vinculas a una sesión, se restará del balance de caja</p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-[#202e3d] flex justify-end gap-3 sticky bottom-0 z-10">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="save" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">
                        {{ $isEditing ? 'Actualizar' : 'Guardar' }} Gasto
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
