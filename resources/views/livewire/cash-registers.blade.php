<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center size-10 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-500 rounded-xl hover:scale-105 transition-transform" title="Volver al Dashboard">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Gestión de Cajas</h2>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sessions.history') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">history</span> Historial
            </a>
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">add</span> Nueva Caja
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($registers as $register)
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between relative group">
            
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $register->is_open ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                    {{ $register->is_open ? 'Abierta' : 'Cerrada' }}
                </span>
            </div>

            <div>
                <div class="size-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 text-slate-600 dark:text-slate-400">
                    <span class="material-symbols-outlined text-2xl">point_of_sale</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $register->name }}</h3>
                <p class="text-sm text-slate-500">ID: #{{ $register->id }}</p>
            </div>

            <div class="mt-6 flex gap-2">
                @if($register->is_open && $register->currentSession)
                    <a href="{{ route('session.active', $register->currentSession->id) }}" class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                        Ver Sesión
                    </a>
                @endif
                <button wire:click="edit({{ $register->id }})" class="flex-1 px-3 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-white rounded-lg text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Editar
                </button>
                <button wire:confirm="¿Eliminar caja?" wire:click="delete({{ $register->id }})" class="px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4 text-slate-900 dark:text-white">{{ $isEditing ? 'Editar Caja' : 'Nueva Caja' }}</h3>
            <input wire:model="name" type="text" placeholder="Nombre (ej: Caja Principal)" class="w-full mb-4 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white">
            <div class="flex justify-end gap-3">
                <button wire:click="$set('showModal', false)" class="text-slate-500 font-bold">Cancelar</button>
                <button wire:click="save" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>