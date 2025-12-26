<div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('cash.index') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors mb-4">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-bold">Volver a Cajas</span>
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Historial de Sesiones</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Consulta cierres de caja anteriores</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Filtros</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Fecha Inicio</label>
                    <input wire:model="startDate" type="date" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-slate-900 dark:text-white focus:ring-0 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Fecha Fin</label>
                    <input wire:model="endDate" type="date" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-slate-900 dark:text-white focus:ring-0 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Caja</label>
                    <select wire:model="selectedCashRegister" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-slate-900 dark:text-white focus:ring-0 focus:border-blue-500">
                        <option value="">Todas</option>
                        @foreach($cashRegisters as $register)
                            <option value="{{ $register->id }}">{{ $register->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="applyFilters" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-all">
                        Aplicar
                    </button>
                    <button wire:click="clearFilters" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white rounded-lg font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Sesiones -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg overflow-hidden">
            @if($sessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Fecha Cierre</th>
                                <th class="text-left py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Caja</th>
                                <th class="text-left py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Cajero</th>
                                <th class="text-left py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Cerrado por</th>
                                <th class="text-right py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Total Ventas</th>
                                <th class="text-right py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Diferencia</th>
                                <th class="text-center py-4 px-6 text-sm font-bold text-slate-600 dark:text-slate-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($sessions as $session)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $session->closed_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-500">
                                            {{ $session->closed_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-900 dark:text-white">
                                        {{ $session->cashRegister->name }}
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-900 dark:text-white">
                                        {{ $session->user->name }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($session->closedBy)
                                            <div class="text-sm text-slate-900 dark:text-white">
                                                {{ $session->closedBy->name }}
                                            </div>
                                            @if($session->user_id !== $session->closed_by_user_id)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-xs font-bold">
                                                    <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                                    Admin
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right text-sm font-bold text-slate-900 dark:text-white">
                                        ${{ number_format($session->closing_amount, 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($session->difference != 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold {{ $session->difference > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                {{ $session->difference > 0 ? '+' : '' }}${{ number_format($session->difference, 2) }}
                                            </span>
                                        @else
                                            <span class="text-slate-500 dark:text-slate-500 text-xs">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="{{ route('sessions.detail', $session->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center py-16 text-slate-400">
                    <span class="material-symbols-outlined text-6xl mb-2 opacity-50">history</span>
                    <p class="text-lg font-bold">No se encontraron sesiones</p>
                    <p class="text-sm">Intenta ajustar los filtros</p>
                </div>
            @endif
        </div>
    </div>
</div>
