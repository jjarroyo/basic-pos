<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-[#101922]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Nexus POS' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="h-full overflow-hidden font-display antialiased text-slate-900 dark:text-white">

        <main class="h-full w-full overflow-y-auto relative">
            {{ $slot }}
        </main>

        <div x-data="{ open: false }" class="fixed bottom-6 left-6 z-50">
            
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="absolute bottom-14 left-0 w-48 mb-2 bg-white dark:bg-[#1A2633] rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden py-1"
                style="display: none;"
            >
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'Usuario' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#253241] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    Perfil
                </a>

                {{-- Opciones de sincronización (solo en modo cliente) --}}
                @if(config('pos.mode') === 'client')
                    <button 
                        @click="open = false; $dispatch('open-sync-logs')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#253241] transition-colors text-left"
                    >
                        <span class="material-symbols-outlined text-[20px]">description</span>
                        Ver Logs
                    </button>

                    <button 
                        @click="open = false; $dispatch('manual-sync')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors text-left"
                    >
                        <span class="material-symbols-outlined text-[20px]">sync</span>
                        Sincronizar Ahora
                    </button>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                @endif

                 <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Cerrar Sesión
                </button>
            </form>

               
            </div>

            <button 
                @click="open = !open"
                class="size-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30 flex items-center justify-center transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <span class="font-bold text-lg">
                    {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                </span>
            </button>
        </div>

        @fluxScripts
    </body>
</html>