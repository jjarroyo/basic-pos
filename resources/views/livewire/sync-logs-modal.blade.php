<div 
    x-data
    @open-sync-logs.window="$wire.openSyncLogs()"
    @manual-sync.window="$wire.manualSync()"
>
    @if($showModal)
    <!-- Modal Overlay -->
    <div 
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        wire:click="closeModal"
    >
        <!-- Modal Content -->
        <div 
            class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col"
            wire:click.stop
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">description</span>
                    Logs de Sincronización
                </h2>
                
                <button 
                    wire:click="closeModal"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Sync Message -->
            @if($syncMessage)
                <div class="mx-6 mt-4 p-3 rounded-lg {{ str_contains($syncMessage, '✅') ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                    {{ $syncMessage }}
                </div>
            @endif

            <!-- Actions Bar -->
            <div class="flex items-center gap-2 p-4 bg-slate-50 dark:bg-slate-800/50">
                <button 
                    wire:click="manualSync" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg flex items-center gap-2 transition-colors"
                >
                    <span class="material-symbols-outlined text-sm" wire:loading.remove>sync</span>
                    <span class="material-symbols-outlined text-sm animate-spin" wire:loading>progress_activity</span>
                    <span wire:loading.remove>Sincronizar Ahora</span>
                    <span wire:loading>Sincronizando...</span>
                </button>
                
                <button 
                    wire:click="loadLogs" 
                    class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg flex items-center gap-2 transition-colors"
                >
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    Actualizar
                </button>
                
                <button 
                    wire:click="resetSyncDates" 
                    wire:confirm="¿Resetear fechas de sincronización? La próxima sincronización descargará todos los datos nuevamente."
                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg flex items-center gap-2 transition-colors"
                >
                    <span class="material-symbols-outlined text-sm">history</span>
                    Reset Fechas
                </button>
                
                <button 
                    wire:click="clearLogs" 
                    wire:confirm="¿Estás seguro de limpiar todos los logs?"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2 transition-colors ml-auto"
                >
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Limpiar
                </button>
            </div>

            <!-- Logs Container -->
            <div class="flex-1 overflow-auto p-6">
                <div class="bg-slate-900 rounded-xl p-4 font-mono text-xs">
                    @forelse($logs as $log)
                        <div class="mb-1 text-slate-300 hover:bg-slate-800 px-2 py-1 rounded whitespace-pre-wrap break-all">
                            {{ $log }}
                        </div>
                    @empty
                        <div class="text-slate-500 text-center py-8">
                            No hay logs disponibles
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Footer Info -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-400">
                <p>💡 Tip: Los logs se actualizan automáticamente después de cada sincronización</p>
                <p class="mt-1">🔍 Mostrando las últimas 50 entradas de sincronización</p>
            </div>
        </div>
    </div>
    @endif
</div>
