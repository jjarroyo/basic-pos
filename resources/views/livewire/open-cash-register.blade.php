<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-[#0f172a] p-4">
    <div class="relative w-full max-w-md bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Back Button -->
        <div class="absolute top-6 left-6 z-10">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center size-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl hover:scale-105 transition-all" title="Volver al Dashboard">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
        </div>

        <div class="bg-orange-600 p-6 text-center">
            <div class="size-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm">
                <span class="material-symbols-outlined text-3xl text-white">lock_open</span>
            </div>
            <h2 class="text-2xl font-bold text-white">Apertura de Caja</h2>
            <p class="text-orange-100 text-sm">Selecciona una terminal para iniciar turno</p>
        </div>

        <div class="p-8 space-y-6">
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Caja Registradora</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($registers as $reg)
                    <button 
                        wire:click="$set('selectedRegisterId', {{ $reg->id }})"
                        class="p-3 rounded-xl border-2 text-left transition-all flex items-center gap-3
                        {{ $selectedRegisterId == $reg->id ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20 ring-1 ring-orange-500' : 'border-slate-200 dark:border-slate-700 hover:border-orange-300' }}">
                        <span class="material-symbols-outlined {{ $selectedRegisterId == $reg->id ? 'text-orange-600' : 'text-slate-400' }}">point_of_sale</span>
                        <span class="font-bold text-sm {{ $selectedRegisterId == $reg->id ? 'text-orange-700 dark:text-orange-400' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $reg->name }}
                        </span>
                    </button>
                    @endforeach
                </div>
                @error('selectedRegisterId') <span class="text-red-500 text-xs mt-1">Selecciona una caja</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dinero en Caja (Base)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                    <input wire:model="amount" type="number" step="0.01" class="w-full pl-8 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-2xl font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
            </div>

            <button wire:click="openRegister" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-600/30 transition-transform active:scale-95 flex items-center justify-center gap-2">
                <span>Abrir Turno</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </div>
    </div>
</div>