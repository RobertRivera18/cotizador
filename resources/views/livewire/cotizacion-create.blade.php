@php
$fmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');

$etiquetaEquipo = function ($e) use ($fmt) {
return implode(' — ', array_filter([
$e->nombre,
$e->marca,
$e->capacidad_kw !== null ? $fmt($e->capacidad_kw) . ' kW' : null,
$e->capacidad_kva !== null ? $fmt($e->capacidad_kva) . ' kVA' : null,
$e->tipo === 'generador' && $e->fases ? ucfirst($e->fases) : null,
]));
};
@endphp

<div class="max-w-7xl mx-auto p-4 sm:p-6 bg-white rounded-xl shadow-sm border border-gray-100 space-y-5 sm:space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 border-b pb-4">
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800">Crear Cotización Manual</h2>
            <p class="text-xs text-gray-500">Para clientes directos, llamadas o cotizaciones personalizadas</p>
        </div>
        <a href="{{ route('admin.cotizaciones.index') }}"
            class="text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap">
            &larr; Volver al listado
        </a>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-5 sm:space-y-6">
        <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
            <div class="min-w-0">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nombre del Cliente *</label>
                <input type="text" maxlength="255" wire:model.blur="nombre_cliente"
                    class="w-full text-base sm:text-sm py-2.5 sm:py-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: Juan Pérez / Empresa S.A.">
                @error('nombre_cliente') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="min-w-0">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Teléfono</label>
                <input type="text" maxlength="50" inputmode="tel" wire:model.blur="telefono"
                    class="w-full text-base sm:text-sm py-2.5 sm:py-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: +593 999 999 999">
                @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="min-w-0 sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                <input type="email" maxlength="255" wire:model.blur="email"
                    class="w-full text-base sm:text-sm py-2.5 sm:py-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="cliente@correo.com">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="min-w-0">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cédula/RUC</label>
                <input type="text" inputmode="numeric" maxlength="13" wire:model.blur="cedula_ruc"
                    class="w-full text-base sm:text-sm py-2.5 sm:py-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="10 dígitos (cédula) o 13 (RUC)">
                @error('cedula_ruc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="min-w-0">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Entorno (Opcional)</label>
                <select wire:model="entorno_id"
                    class="w-full text-base sm:text-sm py-2.5 sm:py-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccionar entorno...</option>
                    @foreach($entornos as $entorno)
                    <option value="{{ $entorno->id }}">{{ $entorno->nombre }}</option>
                    @endforeach
                </select>
                @error('entorno_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- SECCIÓN 2: DETALLE DE COTIZACIÓN -->
        <div class="space-y-3">
            <div class="flex justify-between items-center gap-3">
                <h3 class="text-md font-bold text-gray-700">Ítems de la Cotización</h3>
                <button type="button" wire:click="agregarFila"
                    class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-600 px-3 py-2 sm:py-1.5 rounded-lg font-medium hover:bg-indigo-100 transition whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> Agregar Línea
                </button>
            </div>

            {{-- Una sola estructura: tarjetas apiladas en móvil, tabla desde md --}}
            <div class="md:overflow-x-auto md:border md:border-gray-200 md:rounded-lg">
                <table class="block md:table w-full text-left text-sm text-gray-600">
                    <thead class="hidden md:table-header-group bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="p-3 w-1/3 min-w-[16rem]">Equipo / Servicio</th>
                            <th class="p-3">Descripción / Detalle *</th>
                            <th class="p-3 w-24 text-center">Cant.</th>
                            <th class="p-3 w-32 text-right">P. Unitario ($)</th>
                            <th class="p-3 w-32 text-right">Subtotal ($)</th>
                            <th class="p-3 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="block md:table-row-group space-y-3 md:space-y-0 md:divide-y md:divide-gray-200">
                        @foreach($items as $index => $item)
                        <tr wire:key="item-{{ $index }}"
                            class="grid grid-cols-2 gap-3 p-3 border border-gray-200 rounded-lg bg-white md:p-0 md:border-0 md:rounded-none md:table-row">

                            {{-- Encabezado de tarjeta (solo móvil) --}}
                            <td class="md:hidden col-span-2 flex items-center justify-between -mb-1">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Línea {{ $index +
                                    1 }}</span>
                                @if(count($items) > 1)
                                <button type="button" wire:click="eliminarFila({{ $index }})"
                                    aria-label="Eliminar línea"
                                    class="w-9 h-9 -mr-1.5 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            </td>

                            <td class="block col-span-2 md:col-span-1 md:table-cell md:p-2 md:align-top">
                                <span
                                    class="md:hidden block text-[11px] font-semibold uppercase text-gray-500 mb-1">Equipo
                                    / Servicio</span>
                                {{-- Todos los equipos (generadores y baterías), agrupados por tipo. Es .live,
                                elegirlo rellena descripción y precio --}}
                                <select wire:model.live="items.{{ $index }}.equipo_id" aria-label="Equipo"
                                    class="w-full text-base md:text-xs py-2.5 md:py-2 border-gray-300 rounded-md">
                                    <option value="">Personalizado / Otro</option>
                                    @foreach($equiposPorTipo as $tipoClave => $lista)
                                    <optgroup label="{{ $tipos[$tipoClave] ?? ucfirst($tipoClave) }}">
                                        @foreach($lista as $equipo)
                                        <option value="{{ $equipo->id }}">{{ $etiquetaEquipo($equipo) }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                @error("items.$index.equipo_id") <span class="text-red-500 text-[10px] block">{{
                                    $message }}</span> @enderror
                            </td>

                            <td class="block col-span-2 md:col-span-1 md:table-cell md:p-2 md:align-top">
                                <span
                                    class="md:hidden block text-[11px] font-semibold uppercase text-gray-500 mb-1">Descripción
                                    / Detalle *</span>
                                <input type="text" maxlength="255" wire:model.blur="items.{{ $index }}.descripcion"
                                    placeholder="Ej: Generador Diésel 20kVA + Tablero ATS"
                                    class="w-full text-base md:text-xs py-2.5 md:py-2 border-gray-300 rounded-md">
                                @error("items.$index.descripcion") <span class="text-red-500 text-[10px]">{{ $message
                                    }}</span> @enderror
                            </td>

                            <td class="block md:table-cell md:p-2 md:align-top">
                                <span
                                    class="md:hidden block text-[11px] font-semibold uppercase text-gray-500 mb-1">Cant.</span>
                                <input type="number" min="1" max="9999" step="1" inputmode="numeric"
                                    wire:model.blur="items.{{ $index }}.cantidad"
                                    class="w-full text-base md:text-xs py-2.5 md:py-2 border-gray-300 rounded-md md:text-center">
                                @error("items.$index.cantidad") <span class="text-red-500 text-[10px]">{{ $message
                                    }}</span> @enderror
                            </td>

                            <td class="block md:table-cell md:p-2 md:align-top">
                                <span class="md:hidden block text-[11px] font-semibold uppercase text-gray-500 mb-1">P.
                                    Unitario ($)</span>
                                <input type="number" min="0" step="0.01" inputmode="decimal"
                                    wire:model.blur="items.{{ $index }}.precio_unitario"
                                    class="w-full text-base md:text-xs py-2.5 md:py-2 border-gray-300 rounded-md md:text-right">
                                @error("items.$index.precio_unitario") <span class="text-red-500 text-[10px]">{{
                                    $message }}</span> @enderror
                            </td>

                            <td
                                class="col-span-2 md:col-span-1 flex items-center justify-between md:table-cell md:p-2 md:align-top md:text-right font-medium text-gray-900 md:pt-3 pt-2 border-t border-gray-100 md:border-0">
                                <span class="md:hidden text-[11px] font-semibold uppercase text-gray-500">Subtotal</span>
                                <span class="text-base md:text-sm">${{ number_format((float)($items[$index]['subtotal'] ??
                                    0), 2) }}</span>
                            </td>

                            {{-- Eliminar (solo tabla en md+; en móvil va en el encabezado de la tarjeta) --}}
                            <td class="hidden md:table-cell md:p-2 md:align-top text-center md:pt-3">
                                @if(count($items) > 1)
                                <button type="button" wire:click="eliminarFila({{ $index }})"
                                    aria-label="Eliminar línea" class="text-red-500 hover:text-red-700">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Agregar línea al final (cómodo en móvil con listas largas) --}}
            <button type="button" wire:click="agregarFila"
                class="md:hidden w-full inline-flex items-center justify-center gap-2 text-sm text-indigo-600 border border-dashed border-indigo-200 rounded-lg py-2.5 font-medium hover:bg-indigo-50 transition">
                <i class="fa-solid fa-plus"></i> Agregar línea
            </button>

            @error('items') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
        </div>

        <!-- SECCIÓN 3: TOTAL Y BOTÓN DE GUARDAR -->
        <div
            class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between sm:block text-base sm:text-lg font-bold text-gray-800">
                <span>Total Cotización:</span>
                <span class="text-indigo-600">${{ number_format($this->total, 2) }}</span>
            </div>

            <div class="grid grid-cols-2 sm:flex gap-3">
                <a href="{{ route('admin.cotizaciones.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 sm:py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="guardar"
                    class="px-4 sm:px-5 py-2.5 sm:py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-floppy-disk" wire:loading.remove wire:target="guardar"></i>
                    <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="guardar"></i>
                    Guardar
                    <span class="hidden sm:inline">Cotización</span>
                </button>
            </div>
        </div>
    </form>



@script
<script>
    Livewire.on('validacion-fallida', (event) => {
        const errores = event.errores ?? event.detail?.errores ?? [];

        if (!errores.length) return;

        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 4500,
            timerProgressBar: true,
            width: 'min(370px, 92vw)',
            padding: '14px 16px',
            background: '#ffffff',
            color: '#374151',
            iconColor: '#d97706',
            customClass: {
                popup: 'validation-toast',
                title: 'validation-toast-title',
                htmlContainer: 'validation-toast-content',
                closeButton: 'validation-toast-close',
                timerProgressBar: 'validation-toast-progress'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: 'warning',
            title: errores.length === 1
                ? 'Revisa un campo'
                : `Revisa ${errores.length} campos`,
            html: `
                <div style="
                    font-size: 12px;
                    line-height: 1.5;
                    color: #6b7280;
                    text-align: left;
                ">
                    ${errores.map(error => `
                        <div style="margin-top: 4px;">
                            • ${escapeHtml(error)}
                        </div>
                    `).join('')}
                </div>
            `
        });
    });
</script>
@endscript

@push('css')
<style>
    .validation-toast {
        border: 1px solid #f3f4f6 !important;
        border-radius: 12px !important;
        box-shadow: 0 6px 24px rgba(0, 0, 0, .08) !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
            sans-serif !important;
        max-width: 92vw !important;
    }

    .validation-toast-title {
        font-size: 13px !important;
        font-weight: 600 !important;
        text-align: left !important;
        margin-bottom: 3px !important;
    }

    .validation-toast-content {
        margin: 0 !important;
    }

    .validation-toast-close {
        color: #9ca3af !important;
    }

    .validation-toast-progress {
        background: #d97706 !important;
        height: 3px !important;
    }
</style>
@endpush

</div>`
