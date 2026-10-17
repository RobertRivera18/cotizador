<div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow-sm border border-gray-100 space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Crear Cotización Manual</h2>
            <p class="text-xs text-gray-500">Para clientes directos, llamadas o cotizaciones personalizadas</p>
        </div>
        <a href="{{ route('admin.cotizaciones.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
            &larr; Volver al listado
        </a>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">
        <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nombre del Cliente *</label>
                <input type="text" wire:model="nombre_cliente" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: Juan Pérez / Empresa S.A.">
                @error('nombre_cliente') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Teléfono</label>
                <input type="text" wire:model="telefono" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: +593 999 999 999">
                @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="cliente@correo.com">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Entorno (Opcional)</label>
                <select wire:model="entorno_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccionar entorno...</option>
                    @foreach($entornos as $entorno)
                        <option value="{{ $entorno->id }}">{{ $entorno->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- SECCIÓN 2: DETALLE DE COTIZACIÓN -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="text-md font-bold text-gray-700">Ítems de la Cotización</h3>
                <button type="button" wire:click="agregarFila" class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg font-medium hover:bg-indigo-100 transition">
                    <i class="fa-solid fa-plus"></i> Agregar Línea
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="p-3 w-1/4">Equipo / Servicio</th>
                            <th class="p-3">Descripción / Detalle *</th>
                            <th class="p-3 w-24 text-center">Cant.</th>
                            <th class="p-3 w-32 text-right">P. Unitario ($)</th>
                            <th class="p-3 w-32 text-right">Subtotal ($)</th>
                            <th class="p-3 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="p-2">
                                    <select wire:model.live="items.{{ $index }}.generador_id" class="w-full text-xs border-gray-300 rounded-md">
                                        <option value="">Personalizado / Otro</option>
                                        @foreach($generadores as $gen)
                                            <option value="{{ $gen->id }}">{{ $gen->nombre ?? $gen->modelo }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="text" wire:model.live="items.{{ $index }}.descripcion" placeholder="Ej: Generador Diésel 20kVA + Tablero ATS" class="w-full text-xs border-gray-300 rounded-md">
                                    @error("items.$index.descripcion") <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                </td>
                                <td class="p-2">
                                    <input type="number" min="1" wire:model.live="items.{{ $index }}.cantidad" class="w-full text-xs border-gray-300 rounded-md text-center">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" wire:model.live="items.{{ $index }}.precio_unitario" class="w-full text-xs border-gray-300 rounded-md text-right">
                                </td>
                                <td class="p-2 text-right font-medium text-gray-900">
                                    ${{ number_format((float)($items[$index]['subtotal'] ?? 0), 2) }}
                                </td>
                                <td class="p-2 text-center">
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="eliminarFila({{ $index }})" class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('items') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
        </div>

        <!-- SECCIÓN 3: TOTAL Y BOTÓN DE GUARDAR -->
        <div class="flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="text-lg font-bold text-gray-800">
                Total Cotización: <span class="text-indigo-600">${{ number_format($this->total, 2) }}</span>
            </div>

            <div class="mt-3 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.cotizaciones.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cotización
                </button>
            </div>
        </div>
    </form>
</div>