<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        session()->flash('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<div class="flex flex-col h-full bg-slate-50 dark:bg-[#101922]">
    
    {{-- Header --}}
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1A2633] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mi Perfil</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Actualiza tu información personal</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-8">
        <div class="max-w-2xl mx-auto">
            
            {{-- Profile Information --}}
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Información del Perfil</h2>
                
                <form wire:submit="updateProfileInformation" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                        <input wire:model="name" type="text" required autofocus autocomplete="name"
                            class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input wire:model="email" type="email" required autocomplete="email"
                            class="w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                    Tu dirección de email no está verificada.
                                    <button type="button" wire:click.prevent="resendVerificationNotification" class="underline hover:no-underline">
                                        Haz clic aquí para reenviar el email de verificación.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                        Se ha enviado un nuevo enlace de verificación a tu email.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Delete Account Section --}}
            <livewire:settings.delete-user-form />
        </div>
    </div>
</div>
