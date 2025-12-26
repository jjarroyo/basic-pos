<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Nexus POS</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .error-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full">
            <div class="text-center">
                <!-- Error Icon/Number -->
                <div class="mb-8 error-animation">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 shadow-2xl shadow-blue-500/50">
                        <span class="text-6xl font-bold text-white">@yield('code')</span>
                    </div>
                </div>

                <!-- Error Title -->
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">
                    @yield('title')
                </h1>

                <!-- Error Message -->
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">
                    @yield('message')
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:scale-105">
                            <span class="material-symbols-outlined">dashboard</span>
                            Volver al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:scale-105">
                            <span class="material-symbols-outlined">login</span>
                            Iniciar Sesión
                        </a>
                    @endauth
                    
                    <button onclick="window.history.back()" class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-8 py-3 rounded-xl font-bold transition-all shadow-lg border border-slate-200 dark:border-slate-700 hover:scale-105">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Regresar
                    </button>
                </div>

                <!-- Decorative Elements -->
                <div class="mt-16 flex justify-center gap-4 opacity-50">
                    <div class="w-2 h-2 rounded-full bg-blue-500 pulse-slow"></div>
                    <div class="w-2 h-2 rounded-full bg-blue-500 pulse-slow" style="animation-delay: 0.5s;"></div>
                    <div class="w-2 h-2 rounded-full bg-blue-500 pulse-slow" style="animation-delay: 1s;"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
