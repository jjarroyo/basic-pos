<div class="p-6 max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center size-10 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-500 rounded-xl hover:scale-105 transition-transform" title="Volver al Dashboard">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Caja Registradora</h2>
        </div>
        <a href="{{ route('sessions.history') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">history</span> Historial
        </a>
    </div>

    @if(session('message'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Cash Register Card -->
    @if($register)
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 shadow-lg border border-slate-200 dark:border-slate-700">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined text-4xl">point_of_sale</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $register->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">ID: #{{ $register->id }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold {{ $register->is_open ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-300' }}">
                    <span class="size-2 rounded-full {{ $register->is_open ? 'bg-green-600 animate-pulse' : 'bg-slate-400' }}"></span>
                    {{ $register->is_open ? 'Abierta' : 'Cerrada' }}
                </span>
            </div>

            @if($register->is_open && $register->currentSession)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">info</span>
                        <span class="font-bold text-blue-900 dark:text-blue-300">Sesión Activa</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-600 dark:text-slate-400">Usuario:</span>
                            <span class="font-bold text-slate-900 dark:text-white ml-2">{{ $register->currentSession->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-600 dark:text-slate-400">Apertura:</span>
                            <span class="font-bold text-slate-900 dark:text-white ml-2">{{ $register->currentSession->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-600 dark:text-slate-400">Monto Inicial:</span>
                            <span class="font-bold text-slate-900 dark:text-white ml-2">${{ number_format($register->currentSession->starting_cash, 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-3">
                <button wire:click="edit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">edit</span>
                    Editar Nombre
                </button>
                
                @if($register->is_open && $register->currentSession)
                    <a href="{{ route('session.active', $register->currentSession->id) }}" class="flex-1 px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-bold transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">visibility</span>
                        Ver Sesión Activa
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4 text-slate-900 dark:text-white">Editar Caja Registradora</h3>
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nombre</label>
                <input wire:model="name" type="text" placeholder="Nombre de la caja" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-end gap-3">
                <button wire:click="closeModal" class="text-slate-500 dark:text-slate-400 font-bold hover:text-slate-700 dark:hover:text-slate-200">Cancelar</button>
                <button wire:click="save" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-bold transition-colors">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>