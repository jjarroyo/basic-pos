<div class="group relative col-span-1 sm:col-span-2 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 p-6 text-white shadow-xl shadow-emerald-600/20 transition-all hover:scale-[1.01] hover:shadow-2xl overflow-hidden flex flex-col justify-between min-h-[280px]">
    <!-- Background decoration -->
    <div class="absolute right-0 top-0 opacity-10 -mr-16 -mt-16 rounded-full bg-white/30 p-20 blur-2xl"></div>
    <div class="absolute left-0 bottom-0 opacity-5 -ml-10 -mb-10 rounded-full bg-white/20 p-16 blur-xl"></div>
    
    <!-- Header -->
    <div class="relative z-10 flex items-start justify-between">
        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl inline-flex">
            <span class="material-symbols-outlined text-4xl">point_of_sale</span>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                <span class="size-2 bg-emerald-300 rounded-full animate-pulse"></span>
                {{ $activeSessions }} {{ $activeSessions === 1 ? 'Activa' : 'Activas' }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 mt-4 space-y-4">
        <div>
            <h3 class="text-3xl font-bold mb-1">Cajas Registradoras</h3>
            <p class="text-emerald-100 text-sm">Gestión y control de cajas</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Total Cash -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    <span class="text-xs font-medium text-emerald-100">Efectivo Total</span>
                </div>
                <p class="text-2xl font-bold">${{ number_format($totalCash, 2) }}</p>
            </div>

            <!-- Total Registers -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-lg">inventory</span>
                    <span class="text-xs font-medium text-emerald-100">Total Cajas</span>
                </div>
                <p class="text-2xl font-bold">{{ $cashRegisters->count() }}</p>
            </div>
        </div>

        <!-- Active Registers List -->
        @if($cashRegisters->where('is_open', true)->count() > 0)
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20 max-h-32 overflow-y-auto">
            <p class="text-xs font-bold text-emerald-100 mb-2">CAJAS ABIERTAS</p>
            <div class="space-y-2">
                @foreach($cashRegisters->where('is_open', true) as $register)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="size-1.5 bg-emerald-300 rounded-full"></span>
                        <span class="font-medium">{{ $register->name }}</span>
                    </div>
                    <span class="text-xs text-emerald-100">
                        {{ $register->currentSession?->user?->name ?? 'N/A' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Action Button -->
    <a href="{{ route('cash.index') }}" class="relative z-10 mt-4 w-full bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 group/btn">
        <span>Gestionar Cajas</span>
        <span class="material-symbols-outlined text-xl transform group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
    </a>
</div>
