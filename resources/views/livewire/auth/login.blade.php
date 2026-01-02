<x-layouts.auth>
    <div class="space-y-8">
        <!-- Header -->
        <div class="text-center lg:text-left">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
                Bienvenido de nuevo
            </h2>
            <p class="text-slate-600 dark:text-slate-400">
                Ingresa tus credenciales para acceder al sistema
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">
                        mail
                    </span>
                    <input 
                        id="email"
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus 
                        autocomplete="email"
                        placeholder="usuario@ejemplo.com"
                        class="w-full pl-11 pr-4 py-3 bg-white dark:bg-[#1A2633] border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Contraseña
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" wire:navigate>
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <div class="relative" x-data="{ showPassword: false }">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">
                        lock
                    </span>
                    <input 
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-11 pr-12 py-3 bg-white dark:bg-[#1A2633] border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    >
                    <button 
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                    >
                        <span class="material-symbols-outlined text-xl" x-show="!showPassword">visibility</span>
                        <span class="material-symbols-outlined text-xl" x-show="showPassword" style="display: none;">visibility_off</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    id="remember" 
                    name="remember" 
                    type="checkbox" 
                    {{ old('remember') ? 'checked' : '' }}
                    class="size-4 text-blue-600 bg-white dark:bg-[#1A2633] border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500 focus:ring-2"
                >
                <label for="remember" class="ml-2 text-sm text-slate-700 dark:text-slate-300">
                    Recordarme
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#101922]"
                    data-test="login-button"
                >
                    <span x-show="!loading">Iniciar Sesión</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Iniciando sesión...
                    </span>
                    <span class="material-symbols-outlined text-xl" x-show="!loading">arrow_forward</span>
                </button>
            </div>
        </form>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="text-center pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors" wire:navigate>
                        Regístrate aquí
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-layouts.auth>
