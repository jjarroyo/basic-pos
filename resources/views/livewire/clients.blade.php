<div class="flex flex-col h-full bg-slate-50 dark:bg-[#0f172a]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Clientes</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Directorio de compradores</p>
            </div>
        </div>
        
        <button wire:click="create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">person_add</span>
            Nuevo Cliente
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8">
        
        <div class="max-w-md mb-6 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <span class="material-symbols-outlined">search</span>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o documento..." 
                class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1e293b] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
        </div>

        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-[#0f172a] text-slate-500 dark:text-slate-400 font-semibold text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Documento</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($clients as $client)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $client->name }}</div>
                            <div class="text-xs text-slate-400">{{ $client->address ?? 'Sin dirección' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ $client->document_type }}
                            </span>
                            <span class="ml-2 font-mono text-slate-600 dark:text-slate-400">{{ $client->identification }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            <div class="flex flex-col">
                                <span>{{ $client->email ?? '-' }}</span>
                                <span class="text-xs">{{ $client->phone ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="size-2.5 rounded-full block {{ $client->is_active ? 'bg-green-500' : 'bg-slate-300' }}"></span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $client->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            @if($client->identification !== '222222222222')
                                <button wire:confirm="¿Eliminar cliente?" wire:click="delete({{ $client->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">person_off</span>
                            <p>No se encontraron clientes</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $clients->links() }}
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-[#1e293b]">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre Completo / Razón Social</label>
                    <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tipo</label>
                        <select wire:model="document_type" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="CC">C.C.</option>
                            <option value="NIT">NIT</option>
                            <option value="TI">T.I.</option>
                            <option value="CE">C.E.</option>
                            <option value="PASSPORT">Pasaporte</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Número de Documento</label>
                        <input wire:model="identification" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('identification') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Teléfono</label>
                        <input wire:model="phone" type="tel" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input wire:model="email" type="email" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Dirección</label>
                    <input wire:model="address" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input wire:model="is_active" type="checkbox" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-slate-900 dark:text-slate-300">Cliente Activo</span>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 dark:bg-[#0f172a] flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                <button wire:click="$set('showModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancelar</button>
                <button wire:click="save" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>