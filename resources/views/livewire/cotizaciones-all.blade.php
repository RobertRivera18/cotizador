<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-gray-100">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Cotizaciones</h1>
            <p class="text-sm text-gray-500 mt-1">Solicitudes recibidas para preparar y enviar proformas.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
            <select wire:model.live="filtroEntorno"
                class="bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                <option value="">Todos los entornos</option>
                @foreach ($entornos as $entorno)
                <option value="{{ $entorno->id }}">{{ $entorno->nombre }}</option>
                @endforeach
            </select>
            <div class="relative sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar cliente, teléfono o email..."
                    class="w-full pl-9 pr-4 py-2 bg-gray-50/50 hover:bg-gray-50 focus:bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all">
            </div>
        </div>
    </div>

    {{-- Tabla desktop --}}
    <div class="hidden md:block bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-2xs">
        <table class="min-w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider text-gray-400 font-medium border-b border-gray-100 bg-gray-50/50">
                <tr>
                    <th class="py-3.5 px-4">#</th>
                    <th class="py-3.5 px-4">Fecha</th>
                    <th class="py-3.5 px-4">Cliente</th>
                    <th class="py-3.5 px-4">Entorno</th>
                    <th class="py-3.5 px-4">Capacidad</th>
                    <th class="py-3.5 px-4">Modelo</th>
                    <th class="py-3.5 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-gray-600">
                @forelse ($cotizaciones as $c)
                <tr class="hover:bg-gray-50/50 transition-colors" wire:key="cot-{{ $c->id }}">
                    <td class="py-3 px-4 text-gray-400">{{ $c->id }}</td>
                    <td class="py-3 px-4 whitespace-nowrap">
                        <span class="block text-gray-900">{{ $c->created_at->format('d/m/Y') }}</span>
                        <span class="text-xs text-gray-400">{{ $c->created_at->format('H:i') }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="block font-medium text-gray-900">{{ $c->nombre_cliente }}</span>
                        <span class="text-xs text-gray-400">{{ $c->telefono }}</span>
                    </td>
                    <td class="py-3 px-4">{{ $c->entorno?->nombre ?? '—' }}</td>
                    <td class="py-3 px-4 whitespace-nowrap font-medium text-gray-900">
                        {{ $c->capacidad_recomendada_kva }} kVA
                    </td>
                    <td class="py-3 px-4">
                        @if ($c->generador)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $c->generador->nombre }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">Consulta general</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                        <a href="{{ $this->waLink($c->telefono, $c->nombre_cliente) }}" target="_blank" rel="noopener"
                            title="Contactar por WhatsApp"
                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                        </a>
                        <button wire:click="ver({{ $c->id }})" title="Ver detalle"
                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button
                            wire:click="$dispatch('confirmar-eliminar', { id: {{ $c->id }}, nombre: @js($c->nombre_cliente) })"
                            title="Eliminar"
                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <p class="text-sm">No hay cotizaciones registradas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tarjetas móvil --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:hidden">
        @forelse ($cotizaciones as $c)
        <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-3 shadow-2xs" wire:key="cot-m-{{ $c->id }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-medium text-gray-900 text-sm">{{ $c->nombre_cliente }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $c->telefono }}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $c->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 py-2 border-y border-gray-50 text-xs">
                <div>
                    <span class="text-gray-400 block">Entorno</span>
                    <span class="font-medium text-gray-800">{{ $c->entorno?->nombre ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Capacidad</span>
                    <span class="font-medium text-gray-800">{{ $c->capacidad_recomendada_kva }} kVA</span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-400 block">Modelo</span>
                    <span class="font-medium text-gray-800">{{ $c->generador?->nombre ?? 'Consulta general' }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-1">
                <a href="{{ $this->waLink($c->telefono, $c->nombre_cliente) }}" target="_blank" rel="noopener"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                </a>
                <button wire:click="ver({{ $c->id }})"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fa-solid fa-eye"></i>
                </button>
                <button
                    wire:click="$dispatch('confirmar-eliminar', { id: {{ $c->id }}, nombre: @js($c->nombre_cliente) })"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400 bg-white border border-gray-100 rounded-2xl">
            <p class="text-sm">No hay cotizaciones registradas.</p>
        </div>
        @endforelse
    </div>

    @if ($cotizaciones->hasPages())
    <div>{{ $cotizaciones->links() }}</div>
    @endif

    {{-- ================= MODAL DETALLE ================= --}}
    @if ($this->cotizacion)
    @php $c = $this->cotizacion; @endphp
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-3 sm:p-6 overflow-y-auto"
        wire:click.self="cerrar">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl my-auto border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 flex-shrink-0">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Cotización #{{ $c->id }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Recibida el {{ $c->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                </div>
                <button wire:click="cerrar" type="button"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-200/60 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            {{-- Contenido --}}
            <div class="p-6 overflow-y-auto space-y-6 flex-1">

                {{-- Cliente + Entorno --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-gray-100 p-4 space-y-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Cliente</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $c->nombre_cliente }}</p>
                        <p class="text-sm text-gray-600"><i class="fa-solid fa-phone w-4 text-gray-400"></i> {{ $c->telefono }}</p>
                        <p class="text-sm text-gray-600">
                            <i class="fa-solid fa-envelope w-4 text-gray-400"></i>
                            {{ $c->email ?: 'Sin email' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-100 p-4 space-y-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Requerimiento</p>
                        <p class="text-sm text-gray-600">Entorno:
                            <span class="font-semibold text-gray-900">{{ $c->entorno?->nombre ?? '—' }}</span>
                        </p>
                        <p class="text-sm text-gray-600">Potencia total:
                            <span class="font-semibold text-gray-900">{{ number_format($c->potencia_total_w) }} W</span>
                        </p>
                        <p class="text-sm text-gray-600">Capacidad recomendada:
                            <span class="font-semibold text-gray-900">
                                {{ $c->capacidad_recomendada_kva }} kVA / {{ $c->capacidad_recomendada_kw }} kW
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Modelo elegido --}}
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Modelo de interés</p>
                    @if ($c->generador)
                    <div class="flex items-center gap-4 rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                        @if ($c->generador->imagen)
                        <img src="{{ asset('storage/' . $c->generador->imagen) }}"
                            class="w-16 h-16 object-cover rounded-lg border border-blue-100 flex-shrink-0">
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900">{{ $c->generador->nombre }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 capitalize">
                                {{ $c->generador->capacidad_kva }} kVA · {{ $c->generador->capacidad_kw }} kW · {{ $c->generador->combustible }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] uppercase tracking-wide text-gray-400">Precio lista</p>
                            <p class="text-base font-bold text-gray-900">
                                {{ $c->generador->precio !== null ? '$' . number_format($c->generador->precio, 2) : 'Consultar' }}
                            </p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 rounded-xl border border-dashed border-gray-200 p-4">
                        El cliente no eligió un modelo específico (consulta general). Sugiere uno según la capacidad recomendada.
                    </p>
                    @endif
                </div>

                {{-- Equipos --}}
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">
                        Equipos a conectar ({{ count($this->equiposDetalle) }})
                    </p>
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <table class="min-w-full text-sm text-left">
                            <thead class="text-xs uppercase tracking-wider text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="py-2.5 px-4">Equipo</th>
                                    <th class="py-2.5 px-4 text-center">Cant.</th>
                                    <th class="py-2.5 px-4 text-right">Potencia</th>
                                    <th class="py-2.5 px-4">Prioridad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-600">
                                @forelse ($this->equiposDetalle as $e)
                                <tr>
                                    <td class="py-2.5 px-4 font-medium text-gray-900">{{ $e['nombre'] }}</td>
                                    <td class="py-2.5 px-4 text-center">{{ $e['cantidad'] }}</td>
                                    <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                        {{ $e['potencia_w'] ? number_format($e['potencia_w']) . ' W' : '—' }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        @if ($e['prioridad'] === 'critico')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">Crítico</span>
                                        @elseif ($e['prioridad'] === 'intermitente')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Intermitente</span>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400">Sin equipos registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer con acciones para el asesor --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-3.5 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
                <div x-data="{ copiado: false }">
                    <button type="button"
                        x-on:click="navigator.clipboard.writeText(@js($this->resumenTexto)); copiado = true; setTimeout(() => copiado = false, 2000)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition-colors">
                        <i class="fa-regular fa-copy" x-show="!copiado"></i>
                        <i class="fa-solid fa-check text-emerald-600" x-show="copiado" x-cloak></i>
                        <span x-text="copiado ? 'Copiado' : 'Copiar resumen'"></span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    @if ($c->email)
                    <a href="mailto:{{ $c->email }}?subject={{ rawurlencode('Proforma de generador - Cotización #' . $c->id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-envelope"></i> Email
                    </a>
                    @endif
                    <a href="{{ $this->waLink($c->telefono, $c->nombre_cliente) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-colors">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SweetAlert: notificaciones y confirmación de eliminación --}}
    @script
    <script>
        $wire.on('swal', (e) => {
            const data = Array.isArray(e) ? e[0] : e;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.icon,
                title: data.title,
                text: data.text,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: '!rounded-xl !border !border-gray-100 !shadow-lg',
                    title: '!text-sm !font-semibold !text-gray-900',
                    htmlContainer: '!text-xs !text-gray-500',
                },
            });
        });

        $wire.on('confirmar-eliminar', (e) => {
            const { id, nombre } = Array.isArray(e) ? e[0] : e;

            Swal.fire({
                title: '¿Eliminar cotización?',
                html: `Se eliminará la solicitud de <strong class="text-gray-900">${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                iconColor: '#dc2626',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-trash-can mr-1.5"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                width: '26rem',
                customClass: {
                    popup: '!rounded-2xl !border !border-gray-100 !shadow-2xl !p-6',
                    title: '!text-base !font-semibold !text-gray-900 !p-0 !mt-2',
                    htmlContainer: '!text-sm !text-gray-500 !leading-relaxed !mt-2',
                    actions: '!gap-2 !mt-5 !w-full !justify-end',
                    confirmButton: 'px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors',
                    cancelButton: 'px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors',
                },
                preConfirm: async () => {
                    await $wire.eliminar(id);
                }
            });
        });
    </script>
    @endscript
</div>
