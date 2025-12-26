<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">description</span>
            Logs de Sincronización
        </h2>
        
        <div class="flex gap-2">
            <button 
                wire:click="refresh" 
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors"
            >
                <span class="material-symbols-outlined text-sm">refresh</span>
                Actualizar
            </button>
            
            <button 
                wire:click="clearLogs" 
                wire:confirm="¿Estás seguro de limpiar todos los logs?"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2 transition-colors"
            >
                <span class="material-symbols-outlined text-sm">delete</span>
                Limpiar
            </button>
        </div>
    </div>

    <!-- Log Container -->
    <div class="bg-slate-900 rounded-xl p-4 font-mono text-sm overflow-auto" style="max-height: 600px;">
        @forelse($logs as $log)
            <div class="mb-1 text-slate-300 hover:bg-slate-800 px-2 py-1 rounded">
                {{ $log }}
            </div>
        @empty
            <div class="text-slate-500 text-center py-8">
                No hay logs disponibles
            </div>
        @endforelse
    </div>

    <!-- Auto-refresh info -->
    <div class="mt-4 text-sm text-slate-600 dark:text-slate-400">
        <p>💡 Tip: Los logs se actualizan automáticamente después de cada sincronización</p>
        <p class="mt-1">🔍 Solo se muestran las últimas 50 entradas de sincronización</p>
    </div>
</div>
