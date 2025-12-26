<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Configuración Inicial
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Configura tu sistema POS en pocos pasos
            </p>
        </div>

        <!-- Progress Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="flex items-center {{ $i < 5 ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= $i ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }} font-semibold transition-all duration-300">
                                {{ $i }}
                            </div>
                            <span class="text-xs mt-2 {{ $currentStep >= $i ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                                @if ($i === 1) Empresa
                                @elseif ($i === 2) Usuarios
                                @elseif ($i === 3) Cajas
                                @elseif ($i === 4) Sistema
                                @else Resumen
                                @endif
                            </span>
                        </div>
                        @if ($i < 5)
                            <div class="flex-1 h-1 mx-2 {{ $currentStep > $i ? 'bg-blue-600' : 'bg-gray-300' }} transition-all duration-300"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Wizard Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8">
            <!-- Step 1: Company Information -->
            @if ($currentStep === 1)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Información de la Empresa</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nombre de la Empresa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="company_name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NIT</label>
                            <input type="text" wire:model="company_nit" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_nit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ciudad</label>
                            <input type="text" wire:model="company_city" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dirección</label>
                            <input type="text" wire:model="company_address" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                            <input type="text" wire:model="company_phone" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" wire:model="company_email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sitio Web</label>
                            <input type="url" wire:model="company_website" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('company_website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo de la Empresa</label>
                            <input type="file" wire:model="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @if ($logo)
                                <div class="mt-2">
                                    <img src="{{ $logo->temporaryUrl() }}" class="h-20 rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: User Creation -->
            @if ($currentStep === 2)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Usuarios Vendedores</h2>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">Credenciales por Defecto</h3>
                                <p class="text-sm text-blue-800 dark:text-blue-400">
                                    Los vendedores se crearán automáticamente con:<br>
                                    <strong>Email:</strong> vendedor1@nexus.com, vendedor2@nexus.com, etc.<br>
                                    <strong>Contraseña:</strong> password<br>
                                    <em>Podrás editarlos después desde la gestión de usuarios.</em>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ¿Cuántos vendedores deseas crear? (0-10)
                        </label>
                        <input type="number" wire:model="seller_count" min="0" max="10" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('seller_count') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                        @if ($seller_count > 0)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Se crearán {{ $seller_count }} vendedor(es): 
                                @for ($i = 1; $i <= min($seller_count, 3); $i++)
                                    Vendedor {{ $i }}{{ $i < min($seller_count, 3) ? ', ' : '' }}
                                @endfor
                                @if ($seller_count > 3)
                                    ... y {{ $seller_count - 3 }} más
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 3: Cash Register Creation -->
            @if ($currentStep === 3)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Cajas Registradoras</h2>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">Información</h3>
                                <p class="text-sm text-blue-800 dark:text-blue-400">
                                    Las cajas se crearán automáticamente como:<br>
                                    <strong>Caja 1</strong>, <strong>Caja 2</strong>, <strong>Caja 3</strong>, etc.<br>
                                    <em>Podrás editarlas después desde la gestión de cajas.</em>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ¿Cuántas cajas registradoras deseas crear? (1-10)
                        </label>
                        <input type="number" wire:model="register_count" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('register_count') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                        @if ($register_count > 0)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Se crearán {{ $register_count }} caja(s): 
                                @for ($i = 1; $i <= min($register_count, 3); $i++)
                                    Caja {{ $i }}{{ $i < min($register_count, 3) ? ', ' : '' }}
                                @endfor
                                @if ($register_count > 3)
                                    ... y {{ $register_count - 3 }} más
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 4: System Configuration -->
            @if ($currentStep === 4)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Configuración del Sistema</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tasa de Impuesto (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="tax_rate" step="0.01" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('tax_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Símbolo de Moneda <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="currency_symbol" maxlength="5" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('currency_symbol') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pie de Página del Ticket
                            </label>
                            <textarea wire:model="ticket_footer" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('ticket_footer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 5: Summary -->
            @if ($currentStep === 5)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Resumen de Configuración</h2>
                    
                    <div class="space-y-4">
                        <!-- Company Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Información de la Empresa</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Nombre:</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $company_name }}</dd>
                                </div>
                                @if ($company_nit)
                                    <div>
                                        <dt class="text-gray-600 dark:text-gray-400">NIT:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $company_nit }}</dd>
                                    </div>
                                @endif
                                @if ($company_city)
                                    <div>
                                        <dt class="text-gray-600 dark:text-gray-400">Ciudad:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $company_city }}</dd>
                                    </div>
                                @endif
                                @if ($company_email)
                                    <div>
                                        <dt class="text-gray-600 dark:text-gray-400">Email:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $company_email }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Users -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Usuarios</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                @if ($seller_count > 0)
                                    Se crearán <strong>{{ $seller_count }}</strong> vendedor(es) con credenciales por defecto.
                                @else
                                    No se crearán vendedores adicionales.
                                @endif
                            </p>
                        </div>

                        <!-- Cash Registers -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Cajas Registradoras</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Se crearán <strong>{{ $register_count }}</strong> caja(s) registradora(s).
                            </p>
                        </div>

                        <!-- System Config -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Configuración del Sistema</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Tasa de Impuesto:</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $tax_rate }}%</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Símbolo de Moneda:</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $currency_symbol }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-sm text-green-800 dark:text-green-400">
                            Al hacer clic en "Finalizar Setup", se guardará toda la configuración y serás redirigido al dashboard.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                @if ($currentStep > 1)
                    <button wire:click="previousStep" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Anterior
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < 5)
                    <button wire:click="nextStep" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Siguiente
                    </button>
                @else
                    <button wire:click="completeSetup" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Finalizar Setup
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
