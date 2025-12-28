<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';
    public bool $showModal = false;

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Eliminar Cuenta</h2>
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente.
    </p>

    <button wire:click="$set('showModal', true)" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">
        Eliminar Cuenta
    </button>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">¿Estás seguro?</h3>
                </div>

                <form wire:submit="deleteUser" class="p-6 space-y-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. 
                        Por favor ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
                    </p>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Contraseña</label>
                        <input wire:model="password" type="password" required
                            class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">
                            Eliminar Cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
