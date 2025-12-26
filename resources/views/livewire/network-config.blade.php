<div>
    <!-- Configuration Button -->
    <button 
        wire:click="openModal"
        class="size-12 rounded-full bg-white dark:bg-slate-800 shadow-lg flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 transition-all hover:scale-110 border border-slate-200 dark:border-slate-700"
        title="Configuración de Red"
    >
        <span class="material-symbols-outlined text-slate-600 dark:text-slate-400">settings</span>
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click="closeModal">
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">settings</span>
                    Configuración de Red
                </h2>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl text-sm">
                    @php echo session('message'); @endphp
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl text-sm">
                    @php echo session('error'); @endphp
                </div>
            @endif

            {{-- SOLO MOSTRAR SI ESTAMOS EN MODO SERVIDOR --}}
            @if($mode === 'server')
                <div class="mt-4 p-4 rounded-lg border {{ $serverRunning ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            {{-- Icono de estado --}}
                            <div class="flex-shrink-0">
                                @if($serverRunning)
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            
                            {{-- Texto de estado --}}
                            <div class="ml-3">
                                <h3 class="text-sm font-medium {{ $serverRunning ? 'text-green-800' : 'text-red-800' }}">
                                    @if($serverRunning)
                                        Servidor Activo y Escuchando
                                    @else
                                        El servidor de red parece detenido
                                    @endif
                                </h3>
                                <div class="text-sm {{ $serverRunning ? 'text-green-700' : 'text-red-700' }}">
                                    IP: <span class="font-bold">{{ $localIp }}:{{ $serverPort }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Botón de refrescar (útil si tarda un poco en iniciar) --}}
                        @if(!$serverRunning)
                            <button wire:click="checkServerStatus" 
                                    class="ml-3 text-sm font-medium text-red-600 hover:text-red-500 underline focus:outline-none">
                                Verificar nuevamente
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Mode Selection -->
            <div class="space-y-3 mb-6">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Modo de Operación
                </label>

                <label @class([
                    'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all',
                    'border-blue-500 bg-blue-50 dark:bg-blue-900/20' => $mode === 'standalone',
                    'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' => $mode !== 'standalone'
                ])>
                    <input type="radio" wire:model.live="mode" value="standalone" class="mt-1">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">Independiente</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Un solo punto de venta, sin conexión</p>
                    </div>
                </label>

                <label @class([
                    'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all',
                    'border-blue-500 bg-blue-50 dark:bg-blue-900/20' => $mode === 'server',
                    'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' => $mode !== 'server'
                ])>
                    <input type="radio" wire:model.live="mode" value="server" class="mt-1">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">Servidor</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Otros POS se conectarán a este</p>
                    </div>
                </label>

                <label @class([
                    'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all',
                    'border-blue-500 bg-blue-50 dark:bg-blue-900/20' => $mode === 'client',
                    'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' => $mode !== 'client'
                ])>
                    <input type="radio" wire:model.live="mode" value="client" class="mt-1">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">Cliente</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Conectar a otro POS servidor</p>
                    </div>
                </label>
            </div>

            <!-- Server IP (only for server mode) -->
            @if($mode === 'server')
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl mb-6">
                    <p class="text-sm font-bold text-blue-700 dark:text-blue-400 mb-1">Tu IP Local:</p>
                    <p class="text-2xl font-mono font-bold text-blue-600 dark:text-blue-400">@php echo $localIp; @endphp</p>
                    <p class="text-xs text-blue-600 dark:text-blue-500 mt-2">
                        Otros POS deben conectarse a esta IP
                    </p>
                </div>
            @endif

            <!-- Client Configuration -->
            @if($mode === 'client')
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            IP del Servidor
                        </label>
                        <input 
                            wire:model="serverIp" 
                            type="text" 
                            placeholder="192.168.1.100" 
                            class="w-full px-4 py-3 bg-white dark:bg-[#0f172a] border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Puerto
                        </label>
                        <input 
                            wire:model="serverPort" 
                            type="number" 
                            placeholder="8000" 
                            class="w-full px-4 py-3 bg-white dark:bg-[#0f172a] border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <button 
                        wire:click="testConnection" 
                        class="w-full py-3 px-4 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined">wifi_find</span>
                        Probar Conexión
                    </button>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-3">
                <button 
                    wire:click.prevent="closeModal" 
                    type="button"
                    class="flex-1 py-3 px-4 border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    wire:click.prevent="saveConfig"
                    type="button"
                    class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined">save</span>
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
