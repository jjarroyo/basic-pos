<div class="flex flex-col h-full bg-slate-50 dark:bg-[#0f172a]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Configuración del Sistema</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Personaliza los datos de tu empresa y ticket</p>
            </div>
        </div>
        
        <button wire:click="save" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-xl">save</span>
            Guardar Cambios
        </button>
    </div>

    <div class="flex-1 overflow-auto p-8" x-data="{ activeTab: 'general' }">
        
        <div class="max-w-4xl mx-auto">
            <div class="flex gap-4 mb-8 border-b border-slate-200 dark:border-slate-700 pb-1">
                <button @click="activeTab = 'general'" 
                    :class="activeTab === 'general' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'"
                    class="pb-3 px-2 text-sm font-bold border-b-2 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">store</span> Datos Empresa
                </button>
                <button @click="activeTab = 'fiscal'" 
                    :class="activeTab === 'fiscal' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'"
                    class="pb-3 px-2 text-sm font-bold border-b-2 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">receipt_long</span> Ticket y Fiscal
                </button>
                <a href="{{ route('users.manage') }}" 
                    class="pb-3 px-2 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 text-slate-500 border-transparent hover:text-slate-700">
                    <span class="material-symbols-outlined">group</span> Usuarios
                </a>
                <button @click="activeTab = 'backup'" 
                    :class="activeTab === 'backup' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'"
                    class="pb-3 px-2 text-sm font-bold border-b-2 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">backup</span> Backup
                </button>
                </div>

            @if (session()->has('message'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl flex items-center gap-3 font-bold animate-pulse">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl flex items-center gap-3 font-bold animate-pulse">
                    <span class="material-symbols-outlined">error</span>
                    {{ session('error') }}
                </div>
            @endif

            <div x-show="activeTab === 'general'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Información del Negocio</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nombre de la Empresa *</label>
                            <input wire:model="company_name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                            @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NIT / RUC / ID Fiscal</label>
                            <input wire:model="company_nit" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Teléfono de Contacto</label>
                            <input wire:model="company_phone" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dirección Física</label>
                            <input wire:model="company_address" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ciudad</label>
                            <input wire:model="company_city" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Correo Electrónico</label>
                            <input wire:model="company_email" type="email" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sitio Web</label>
                            <input wire:model="company_website" type="url" placeholder="https://www.ejemplo.com" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Logo de la Empresa</h3>
                    
                    <div class="space-y-4">
                        @if($current_logo)
                            <div class="flex items-center gap-4">
                                 <x-image-display 
                                    :path="$current_logo" 
                                    class="size-12 rounded-lg object-cover" 
                                />
                                <div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Logo actual</p>
                                    <p class="text-xs text-slate-500">Sube una nueva imagen para reemplazarlo</p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                {{ $current_logo ? 'Cambiar Logo' : 'Subir Logo' }}
                            </label>
                            <input wire:model="logo" type="file" accept="image/*" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-slate-500 mt-1">Formatos: JPG, PNG. Máximo 2MB</p>
                        </div>

                        @if($logo)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">info</span>
                                    Nuevo logo seleccionado. Haz clic en "Guardar Cambios" para aplicar.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'fiscal'" class="space-y-6 animate-fade-in" style="display: none;">
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Configuración Fiscal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Impuesto por Defecto (%)</label>
                            <div class="relative">
                                <input wire:model="tax_rate" type="number" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 pr-10 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                <span class="absolute right-4 top-3 text-slate-500 font-bold">%</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Se aplicará a las nuevas ventas.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Símbolo de Moneda</label>
                            <input wire:model="currency_symbol" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Personalización del Ticket</h3>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pie de Página del Ticket</label>
                        <textarea wire:model="ticket_footer" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none resize-none dark:text-white" placeholder="Ej: Gracias por su visita..."></textarea>
                        <p class="text-xs text-slate-500 mt-1">Este mensaje aparecerá al final de cada impresión.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Cajón de Dinero</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input wire:model="enable_cash_drawer" type="checkbox" class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Abrir cajón automáticamente en ventas en efectivo</span>
                                        <p class="text-xs text-slate-500 mt-1">Cuando está habilitado, el cajón se abrirá automáticamente al confirmar una venta en efectivo.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Comando ESC/POS del Cajón</label>
                            <input wire:model="cash_drawer_command" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-mono text-sm">
                            <p class="text-xs text-slate-500 mt-1">Comando estándar: \x1B\x70\x00\x19\xFA (ESC p 0 25 250). Solo modifica si tu impresora requiere un comando diferente.</p>
                        </div>

                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg mt-0.5">info</span>
                                <span>El comando se enviará a la impresora térmica cuando se imprima el ticket de una venta en efectivo. Asegúrate de que tu impresora tenga un cajón de dinero conectado.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Notificaciones por Email</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input wire:model.live="email_notifications_enabled" type="checkbox" class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Enviar comprobante de venta por email</span>
                                        <p class="text-xs text-slate-500 mt-1">Cuando está habilitado, se enviará automáticamente un email con el detalle de cada venta confirmada.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div x-show="$wire.email_notifications_enabled" class="animate-fade-in">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email del Destinatario</label>
                            <input wire:model="email_notifications_recipient" type="email" placeholder="ejemplo@correo.com" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                            @error('email_notifications_recipient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-slate-500 mt-1">Los comprobantes de venta se enviarán a este correo electrónico.</p>
                        </div>

                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg mt-0.5">info</span>
                                <span>El envío de emails se realiza en segundo plano y no afectará el flujo de ventas.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'backup'" class="space-y-6 animate-fade-in" style="display: none;">
                
                <!-- Automatic Backup Configuration -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Configuración de Backups Automáticos</h3>
                    
                    <div class="space-y-6">
                        <!-- Enable Toggle -->
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input wire:model.live="backup_enabled" type="checkbox" class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Habilitar Copias Automáticas</span>
                                        <p class="text-xs text-slate-500 mt-1">El sistema realizará copias de seguridad automáticamente según la frecuencia programada.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div x-show="$wire.backup_enabled" class="space-y-6 animate-fade-in">
                            <!-- Frequency & Time -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Frecuencia</label>
                                    <select wire:model.live="backup_frequency" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                        <option value="startup">Al Iniciar la Aplicación</option>
                                        {{-- <option value="daily">Diaria (Requiere App Abierta)</option> --}}
                                    </select>
                                </div>
                                <div x-show="$wire.backup_frequency === 'daily'">
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Hora de Ejecución</label>
                                    <input wire:model="backup_time" type="time" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                </div>
                            </div>

                            <!-- Storage Type -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ubicación del Backup</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-[#0f172a] p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex-1">
                                        <input wire:model.live="backup_storage_type" type="radio" value="local" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Local (Carpeta)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-[#0f172a] p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex-1">
                                        <input wire:model.live="backup_storage_type" type="radio" value="s3" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Remoto (S3 / MinIO)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Local Settings -->
                            <div x-show="$wire.backup_storage_type === 'local'" class="animate-fade-in">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ruta de la Carpeta</label>
                                <input wire:model="backup_local_path" type="text" placeholder="Ej: C:\Backups" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                @error('backup_local_path') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- S3 Settings -->
                            <div x-show="$wire.backup_storage_type === 's3'" class="space-y-4 animate-fade-in">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Access Key</label>
                                        <input wire:model="backup_s3_access_key" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                        @error('backup_s3_access_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Secret Key</label>
                                        <input wire:model="backup_s3_secret_key" type="password" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                        @error('backup_s3_secret_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Bucket Name</label>
                                        <input wire:model="backup_s3_bucket" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                        @error('backup_s3_bucket') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Region</label>
                                        <input wire:model="backup_s3_region" type="text" placeholder="us-east-1" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                        @error('backup_s3_region') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Endpoint (Opcional - Para MinIO/DigitalOcean)</label>
                                        <input wire:model="backup_s3_endpoint" type="text" placeholder="Ej: https://sfo2.digitaloceanspaces.com" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                                    </div>
                                </div>
                                
                                <button type="button" wire:click="testS3Connection" class="text-sm font-bold text-blue-600 hover:text-blue-700 underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">wifi</span>
                                    Probar Conexión S3
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Database Information -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Información de la Base de Datos</h3>
                    
                    @if($backupInfo)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-slate-50 dark:bg-[#0f172a] rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="material-symbols-outlined text-blue-600">storage</span>
                                    <span class="text-sm font-bold text-slate-500 dark:text-slate-400">Tamaño</span>
                                </div>
                                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $backupInfo['size'] }}</p>
                            </div>
                            
                            <div class="p-4 bg-slate-50 dark:bg-[#0f172a] rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="material-symbols-outlined text-blue-600">schedule</span>
                                    <span class="text-sm font-bold text-slate-500 dark:text-slate-400">Última Modificación</span>
                                </div>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $backupInfo['modified'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Create Backup -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Crear Backup</h3>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg mt-0.5">info</span>
                                <span>Se descargará una copia completa de la base de datos actual. Guárdala en un lugar seguro para poder restaurarla en caso necesario.</span>
                            </p>
                        </div>

                        <button wire:click="createBackup" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
                            <span class="material-symbols-outlined">download</span>
                            Descargar Backup
                        </button>
                    </div>
                </div>

                <!-- Restore Backup -->
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-2">Restaurar Backup</h3>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <p class="text-sm text-amber-700 dark:text-amber-400 flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg mt-0.5">warning</span>
                                <span><strong>¡Advertencia!</strong> Al restaurar un backup, todos los datos actuales serán reemplazados. Se creará un backup automático antes de la restauración.</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Seleccionar Archivo de Backup</label>
                            <input wire:model="backupFile" type="file" accept=".sqlite,.db" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f172a] px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('backupFile') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            <p class="text-xs text-slate-500 mt-1">Formatos: .sqlite, .db. Máximo 50MB</p>
                        </div>

                        @if($backupFile)
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <p class="text-sm text-green-700 dark:text-green-400 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                    Archivo seleccionado: {{ $backupFile->getClientOriginalName() }}
                                </p>
                            </div>

                            <button 
                                wire:click="restore" 
                                onclick="return confirm('¿Estás seguro de que deseas restaurar este backup? Todos los datos actuales serán reemplazados.')"
                                class="w-full flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-amber-600/30">
                                <span class="material-symbols-outlined">restore</span>
                                Restaurar Base de Datos
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>