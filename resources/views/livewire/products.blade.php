<div class="flex flex-col h-full bg-slate-50 dark:bg-[#101922]">
    
    <div class="px-8 py-6 flex items-center justify-between bg-white dark:bg-[#1A2633] border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Productos</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Administra tu inventario y precios</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="downloadTemplate" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors" title="Descargar Plantilla">
                <span class="material-symbols-outlined text-xl">download</span>
            </button>

            <button wire:click="openImportModal" class="p-2 text-slate-500 hover:text-green-600 hover:bg-slate-100 rounded-lg transition-colors" title="Importar Excel">
                <span class="material-symbols-outlined text-xl">upload_file</span>
            </button>

            <button wire:click="create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/30">
                <span class="material-symbols-outlined text-xl">add</span>
                Nuevo
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-8">
        
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1 max-w-md relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o código..." 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
            </div>

            <select wire:model.live="categoryFilter" class="w-full md:w-64 px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-[#202e3d] text-slate-500 dark:text-slate-400 font-semibold text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Producto</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4">Precio Venta</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#253241] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-image-display 
                                    :path="$product->image" 
                                    class="size-12 rounded-lg object-cover" 
                                />
                                
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $product->barcode ?? 'S/N' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->category)
                                <span class="px-2 py-1 rounded-md text-xs font-bold text-white shadow-sm" style="background-color: {{ $product->category->color }}">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            ${{ number_format($product->selling_price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="{{ $product->stock <= $product->min_stock ? 'text-red-500 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $product->stock }} un.
                                </span>
                                @if($product->stock <= $product->min_stock)
                                    <span class="text-[10px] text-red-400">Bajo Stock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="size-2.5 rounded-full block {{ $product->is_active ? 'bg-green-500' : 'bg-slate-300' }}"></span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $product->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button wire:confirm="¿Borrar producto?" wire:click="delete({{ $product->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">inventory_2</span>
                            <p>No se encontraron productos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-[#1A2633] z-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $isEditing ? 'Editar Producto' : 'Nuevo Producto' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre del Producto</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Código de Barras</label>
                                <input wire:model="barcode" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-mono">
                                @error('barcode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Categoría</label>
                                <select wire:model="category_id" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Seleccionar...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
                            <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#202e3d] text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>

                        <div 
                            x-data="{ 
                                uploading: false,
                                async processImage(event) {
                                    let file = event.target.files[0];
                                    if (!file) return;

                                    this.uploading = true;

                                    // 1. Configuración: Máximo ancho 800px, Calidad 80%
                                    const maxWidth = 800;
                                    const quality = 0.8;

                                    // 2. Crear lector de imagen
                                    const reader = new FileReader();
                                    reader.readAsDataURL(file);
                                    
                                    reader.onload = (e) => {
                                        const img = new Image();
                                        img.src = e.target.result;
                                        
                                        img.onload = () => {
                                            // 3. Calcular nuevas dimensiones
                                            let width = img.width;
                                            let height = img.height;

                                            if (width > maxWidth) {
                                                height *= maxWidth / width;
                                                width = maxWidth;
                                            }

                                            // 4. Dibujar en Canvas (Redimensionar)
                                            const canvas = document.createElement('canvas');
                                            canvas.width = width;
                                            canvas.height = height;
                                            const ctx = canvas.getContext('2d');
                                            ctx.drawImage(img, 0, 0, width, height);

                                            // 5. Convertir a Blob (WebP) y Subir
                                            canvas.toBlob((blob) => {
                                                const newFile = new File([blob], 'optimized.webp', { type: 'image/webp' });
                                                
                                                // Subida manual a Livewire
                                                @this.upload('image', newFile, 
                                                    (uploadedFilename) => { this.uploading = false; }, 
                                                    () => { this.uploading = false; alert('Error subiendo imagen'); } 
                                                );
                                            }, 'image/webp', quality);
                                        };
                                    };
                                }
                            }"
                        >
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Imagen</label>
                            
                            <div class="flex items-center gap-4">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="size-20 rounded-lg object-cover border border-slate-200 shadow-sm">
                                @elseif($existingImage)
                                    <x-image-display 
                                    :path="$existingImage" 
                                    class="size-12 rounded-lg object-cover" 
                                />
                                @else
                                    <div class="size-20 rounded-lg bg-slate-100 dark:bg-[#202e3d] border border-dashed border-slate-300 flex items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined">image</span>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        accept="image/*"
                                        @change="processImage"
                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                    >
                                    <p class="text-xs text-slate-400 mt-1" x-show="!uploading">Se optimizará automáticamente a WebP.</p>
                                    
                                    <div x-show="uploading" class="text-blue-600 text-xs font-bold mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm animate-spin">sync</span>
                                        Optimizando y subiendo...
                                    </div>
                                    
                                    @error('image') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 bg-slate-50 dark:bg-[#202e3d] p-4 rounded-xl">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-2 border-b pb-2 dark:border-slate-600">Inventario y Costos</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Precio Costo</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">$</span>
                                    <input wire:model="cost_price" type="number" step="0.01" class="w-full pl-8 pr-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                @error('cost_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Precio Venta</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">$</span>
                                    <input wire:model="selling_price" type="number" step="0.01" class="w-full pl-8 pr-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                @error('selling_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Stock Actual</label>
                                <input wire:model="stock" type="number" class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Stock Mínimo</label>
                                <input wire:model="min_stock" type="number" class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-[#1A2633] text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div class="pt-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-slate-900 dark:text-slate-300">Producto Activo para Venta</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-[#202e3d] flex justify-end gap-3 sticky bottom-0 z-10">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancelar</button>
                    <button wire:click="save" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-600/30 transition-all">Guardar Producto</button>
                </div>
            </div>
        </div>
    @endif

    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Importar Productos</h3>
            
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center bg-slate-50 dark:bg-[#202e3d]">
                <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">cloud_upload</span>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Arrastra tu archivo Excel aquí o haz clic para buscar</p>
                
                <input wire:model="importFile" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                
                <div wire:loading wire:target="importFile" class="mt-2 text-blue-600 text-sm font-bold">
                    Cargando archivo...
                </div>
            </div>

            @error('importFile') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showImportModal', false)" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg">Cancelar</button>
                <button wire:click="importExcel" wire:loading.attr="disabled" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg shadow-green-600/30">
                    <span wire:loading.remove wire:target="importExcel">Procesar Importación</span>
                    <span wire:loading wire:target="importExcel">Procesando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showResultsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1A2633] rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]"
                x-data="{ activeTab: 'inserted' }">
                
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-[#1A2633]">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Resultado de Importación</h3>
                        <p class="text-xs text-slate-500">Resumen del proceso masivo</p>
                    </div>
                    <button wire:click="$set('showResultsModal', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-[#202e3d]">
                    <button @click="activeTab = 'inserted'" 
                        :class="activeTab === 'inserted' ? 'border-green-500 text-green-600 bg-white dark:bg-[#1A2633]' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="flex-1 py-3 text-sm font-bold border-b-2 transition-colors flex items-center justify-center gap-2">
                        <span class="size-2 rounded-full bg-green-500"></span>
                        Insertados ({{ count($importStats['created']) }})
                    </button>
                    <button @click="activeTab = 'updated'" 
                        :class="activeTab === 'updated' ? 'border-blue-500 text-blue-600 bg-white dark:bg-[#1A2633]' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="flex-1 py-3 text-sm font-bold border-b-2 transition-colors flex items-center justify-center gap-2">
                        <span class="size-2 rounded-full bg-blue-500"></span>
                        Actualizados ({{ count($importStats['updated']) }})
                    </button>
                    <button @click="activeTab = 'failed'" 
                        :class="activeTab === 'failed' ? 'border-red-500 text-red-600 bg-white dark:bg-[#1A2633]' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="flex-1 py-3 text-sm font-bold border-b-2 transition-colors flex items-center justify-center gap-2">
                        <span class="size-2 rounded-full bg-red-500"></span>
                        Fallidos ({{ count($importStats['failed']) }})
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-[#101922]">
                    
                    <div x-show="activeTab === 'inserted'" style="display: none;">
                        @if(count($importStats['created']) > 0)
                            <ul class="space-y-2">
                                @foreach($importStats['created'] as $item)
                                    <li class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1A2633] p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-green-500">check_circle</span>
                                        <span class="font-bold">{{ $item['name'] }}</span>
                                        <span class="text-slate-400 text-xs">({{ $item['barcode'] ?? 'Sin código' }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-slate-400 py-8">No se crearon productos nuevos.</p>
                        @endif
                    </div>

                    <div x-show="activeTab === 'updated'" style="display: none;">
                        @if(count($importStats['updated']) > 0)
                            <ul class="space-y-2">
                                @foreach($importStats['updated'] as $item)
                                    <li class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1A2633] p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-blue-500">sync</span>
                                        <span class="font-bold">{{ $item['name'] }}</span>
                                        <span class="text-slate-400 text-xs">({{ $item['barcode'] }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-slate-400 py-8">No se actualizaron productos existentes.</p>
                        @endif
                    </div>

                    <div x-show="activeTab === 'failed'" style="display: none;">
                        @if(count($importStats['failed']) > 0)
                            <ul class="space-y-2">
                                @foreach($importStats['failed'] as $item)
                                    <li class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300 bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-200 dark:border-red-800/30">
                                        <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                                        <div>
                                            <div class="font-bold text-red-700 dark:text-red-400">
                                                Fila {{ $item['row'] }}: {{ $item['name'] }}
                                            </div>
                                            <div class="text-xs text-red-600 dark:text-red-300 mt-1">
                                                {{ $item['error'] }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-green-500">
                                <span class="material-symbols-outlined text-4xl mb-2">thumb_up</span>
                                <p>¡Todo perfecto! No hubo errores.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-[#1A2633] border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button wire:click="$set('showResultsModal', false)" class="px-6 py-2 bg-slate-900 dark:bg-slate-700 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>