<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Iniciar Sesión - Nexus POS' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="h-full font-display antialiased bg-slate-50 dark:bg-[#101922]">
        <div class="min-h-full flex">
            <!-- Left Side - Branding (Hidden on mobile) -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-700 dark:to-blue-900 relative overflow-hidden">
                <!-- Decorative Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
                </div>

                <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-3">
                        <div class="size-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">point_of_sale</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Nexus POS</h1>
                            <p class="text-sm text-blue-100">Sistema de Punto de Venta</p>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="size-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">speed</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Rápido y Eficiente</h3>
                                <p class="text-blue-100 text-sm">Procesa ventas en segundos con nuestra interfaz optimizada</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="size-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">inventory</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Control de Inventario</h3>
                                <p class="text-blue-100 text-sm">Gestiona tu stock en tiempo real</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="size-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">analytics</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Reportes Detallados</h3>
                                <p class="text-blue-100 text-sm">Analiza tus ventas con reportes completos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Quote -->
                    <div class="text-sm text-blue-100">
                        <p>"La mejor herramienta para gestionar tu negocio"</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="flex-1 flex items-center justify-center p-8 lg:p-12">
                <div class="w-full max-w-md">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                        <div class="size-12 bg-blue-600 dark:bg-blue-700 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-white">point_of_sale</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Nexus POS</h1>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
