<div>
    @if($mode === 'client')
    <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm p-4 border border-slate-200 dark:border-slate-700">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">sync</span>
                Estado de Sincronización
            </h3>
            <button 
                wire:click="checkStatus" 
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                title="Actualizar estado"
            >
                <span class="material-symbols-outlined text-lg">refresh</span>
            </button>
        </div>

        <!-- Status Indicators -->
        <div class="space-y-2 mb-3">
            <!-- Server Status -->
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-400">Servidor:</span>
                <span class="flex items-center gap-1 {{ $isOnline ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <span class="size-2 rounded-full {{ $isOnline ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $isOnline ? 'En línea' : 'Desconectado' }}
                </span>
            </div>

            <!-- Pending Sales -->
            @if($pendingSales > 0)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-400">Ventas pendientes:</span>
                <span class="font-bold text-orange-600 dark:text-orange-400">{{ $pendingSales }}</span>
            </div>
            @endif

            <!-- Pending Sessions -->
            @if($pendingSessions > 0)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-400">Sesiones pendientes:</span>
                <span class="font-bold text-orange-600 dark:text-orange-400">{{ $pendingSessions }}</span>
            </div>
            @endif

            <!-- Last Sync -->
            @if($lastSync)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-400">Última sincronización:</span>
                <span class="text-xs text-slate-500 dark:text-slate-500">{{ $lastSync->diffForHumans() }}</span>
            </div>
            @endif
        </div>

        <!-- Sync Button -->
        <button 
            wire:click="syncNow"
            class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>
                <span class="material-symbols-outlined text-sm">sync</span>
                Sincronizar Ahora
            </span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sincronizando...
            </span>
        </button>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mt-3 p-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-xs">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-xs">
                {{ session('error') }}
            </div>
        @endif
    </div>
    @endif
</div>
