<div class="flex flex-col h-full bg-slate-50 dark:bg-[#101922]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1A2633] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Categorías</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Gestiona las clasificaciones de tus productos</p>
            </div>
        </div>
        
        <button wire:click="create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">add</span>
            Nueva Categoría
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8">
        
        <div class="max-w-md mb-6 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <span class="material-symbols-outlined">search</span>
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar categorías..." 
                class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm transition-all">
        </div>

        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-[#202e3d] text-slate-500 dark:text-slate-400 font-semibold text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Color</th>
                        <th class="px-6 py-4">Nombre</th>
                        <th class="px-6 py-4">Descripción</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#253241] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="size-8 rounded-lg shadow-sm border border-black/10" style="background-color: {{ $category->color }}"></div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $category->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                                {{ $category->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $category->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button wire:confirm="¿Estás seguro de eliminar esta categoría?" wire:click="delete({{ $category->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">category</span>
                            <p>No se encontraron categorías</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                    <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>

                <div class="flex gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Color Distintivo</label>
                        <div class="flex items-center gap-2">
                            <input wire:model="color" type="color" class="h-10 w-full rounded cursor-pointer border-0 p-0 bg-transparent">
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Estado</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input wire:model="is_active" type="checkbox" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-slate-900 dark:text-slate-300">Activo</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 dark:bg-[#202e3d] flex justify-end gap-3">
                <button wire:click="$set('showModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancelar</button>
                <button wire:click="save" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>