<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Configuración de la Empresa</h2>
        <p class="text-gray-600 mt-1">Información requerida para facturación electrónica DIAN</p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        
        {{-- Información Básica --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Información Básica</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social *</label>
                    <input type="text" wire:model="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial</label>
                    <input type="text" wire:model="trade_name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIT *</label>
                    <input type="text" wire:model="nit" placeholder="900123456" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('nit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dígito de Verificación *</label>
                    <input type="text" wire:model="dv" maxlength="1" placeholder="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('dv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Régimen Fiscal --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏛️ Régimen Fiscal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Régimen Tributario *</label>
                    <select wire:model="regime_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="comun">Régimen Común</option>
                        <option value="simplificado">Régimen Simplificado</option>
                    </select>
                    @error('regime_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Códigos de Responsabilidad Fiscal</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="responsibility_codes" value="O-13" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <span class="ml-2 text-sm text-gray-700">O-13 - Gran contribuyente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="responsibility_codes" value="O-15" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <span class="ml-2 text-sm text-gray-700">O-15 - Autorretenedor</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="responsibility_codes" value="O-23" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <span class="ml-2 text-sm text-gray-700">O-23 - Agente de retención IVA</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="responsibility_codes" value="O-47" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <span class="ml-2 text-sm text-gray-700">O-47 - Régimen simple de tributación</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="responsibility_codes" value="R-99-PN" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <span class="ml-2 text-sm text-gray-700">R-99-PN - Responsabilidades régimen común</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Información de Contacto --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📞 Información de Contacto</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" wire:model="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" wire:model="phone" placeholder="+57 601 1234567" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                    <input type="url" wire:model="website" placeholder="https://www.miempresa.com" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
            </div>
        </div>

        {{-- Dirección Fiscal --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Dirección Fiscal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa *</label>
                    <input type="text" wire:model="address" placeholder="Calle 123 # 45-67 Oficina 801" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                    <input type="text" wire:model="city" placeholder="Bogotá D.C." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código DANE Ciudad</label>
                    <input type="text" wire:model="city_code" placeholder="11001" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <span class="text-xs text-gray-500">Consulta códigos en la guía de referencia</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                    <input type="text" wire:model="department" placeholder="Cundinamarca" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('department') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código DANE Departamento</label>
                    <input type="text" wire:model="department_code" placeholder="11" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">País *</label>
                    <input type="text" wire:model="country" placeholder="CO" maxlength="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                    <input type="text" wire:model="postal_code" placeholder="110111" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
            </div>
        </div>

        {{-- Información Comercial --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏢 Información Comercial y Legal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código CIIU</label>
                    <input type="text" wire:model="economic_activity_code" placeholder="4711" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <span class="text-xs text-gray-500">Código de actividad económica</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción Actividad Económica</label>
                    <input type="text" wire:model="economic_activity_description" placeholder="Comercio al por menor" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula Mercantil</label>
                    <input type="text" wire:model="merchant_registration" placeholder="12345678" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Matrícula</label>
                    <input type="date" wire:model="merchant_registration_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
            </div>
        </div>

        {{-- Información Adicional --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📝 Información Adicional</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nota al Pie de Factura</label>
                <textarea wire:model="invoice_footer_note" rows="3" placeholder="Gracias por su compra..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                <span class="text-xs text-gray-500">Este texto aparecerá al final de cada factura</span>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end space-x-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition duration-150">
                💾 Guardar Información
            </button>
        </div>
    </form>
</div>
