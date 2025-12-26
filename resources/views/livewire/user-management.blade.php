<div class="flex flex-col h-full bg-slate-50 dark:bg-[#0f172a]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('config') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Gestión de Usuarios</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Administra los usuarios del sistema</p>
            </div>
        </div>
        
        <button wire:click="openCreateModal" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">person_add</span>
            Nuevo Usuario
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8">
        <div class="max-w-6xl mx-auto">
            
            @if (session()->has('message'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl flex items-center gap-3 font-bold animate-pulse">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl flex items-center gap-3 font-bold">
                    <span class="material-symbols-outlined">error</span>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-[#0f172a] border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                                {{ $user->initials() }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $roleName = $user->roles->first()?->name ?? 'Sin rol';
                                            $roleColor = $roleName === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleColor }}">
                                            {{ ucfirst($roleName) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->is_active ?? true)
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 flex items-center gap-1 w-fit">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 flex items-center gap-1 w-fit">
                                                <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="openEditModal({{ $user->id }})" class="p-2 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors text-blue-600 dark:text-blue-400" title="Editar">
                                                <span class="material-symbols-outlined text-xl">edit</span>
                                            </button>
                                            <button wire:click="toggleActive({{ $user->id }})" class="p-2 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded-lg transition-colors text-yellow-600 dark:text-yellow-400" title="{{ ($user->is_active ?? true) ? 'Desactivar' : 'Activar' }}">
                                                <span class="material-symbols-outlined text-xl">{{ ($user->is_active ?? true) ? 'block' : 'check_circle' }}</span>
                                            </button>
                                            @if($user->id !== auth()->user()->id)
                                                <button wire:click="deleteUser({{ $user->id }})" onclick="return confirm('¿Estás seguro de eliminar este usuario?')" class="p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors text-red-600 dark:text-red-400" title="Eliminar">
                                                    <span class="material-symbols-outlined text-xl">delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <span class="material-symbols-outlined text-6xl mb-4 block opacity-50">group_off</span>
                                        <p class="font-bold">No hay usuarios registrados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700 px-8 py-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ $editMode ? 'Editar Usuario' : 'Nuevo Usuario' }}
                    </h2>
                    <button wire:click="closeModal" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-slate-500">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nombre Completo *</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Correo Electrónico *</label>
                            <input wire:model="email" type="email" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Contraseña {{ $editMode ? '(dejar en blanco para no cambiar)' : '*' }}</label>
                            <input wire:model="password" type="password" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Confirmar Contraseña {{ $editMode ? '' : '*' }}</label>
                            <input wire:model="password_confirmation" type="password" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Rol *</label>
                            <select wire:model="role" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                <option value="">Seleccionar rol...</option>
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-bold text-slate-700 dark:text-slate-300">Usuario Activo</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" wire:click="closeModal" class="flex-1 px-6 py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-blue-600/30">
                            {{ $editMode ? 'Actualizar' : 'Crear' }} Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
