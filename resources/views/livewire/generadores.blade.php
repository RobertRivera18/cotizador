<div class="space-y-6">
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @php
        $tipos = [
           'generador'   => ['label' => 'Generador', 'icon' => 'fa-bolt', 'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
           'bateria'     => ['label' => 'Batería', 'icon' => 'fa-battery-three-quarters', 'badge' => 'bg-sky-50 text-sky-700 ring-sky-600/20'],
           'inversor'    => ['label' => 'Inversor', 'icon' => 'fa-exchange-alt', 'badge' => 'bg-purple-50 text-purple-700 ring-purple-600/20'],
           'panel solar' => ['label' => 'Panel Solar', 'icon' => 'fa-solar-panel', 'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
                 ];
        $fasesLbl = ['monofasico' => 'Monofásico', 'trifasico' => 'Trifásico'];
        $combLbl  = ['gasolina' => 'Gasolina', 'diesel' => 'Diésel', 'gas' => 'Gas'];

        $num    = fn ($v) => ($v === null || $v === '') ? null : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
        $dinero = fn ($v) => $v !== null ? '$' . number_format((float) $v, 2) : '—';

        $lbl   = 'block text-xs font-semibold text-gray-700 mb-1.5';
        $sec   = 'text-[11px] font-semibold uppercase tracking-wider text-gray-400';
        $input = 'w-full bg-gray-50/50 border rounded-lg px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:bg-white focus:outline-none focus:ring-1 transition-colors';
        $ok    = 'border-gray-200 focus:ring-gray-900 focus:border-gray-900';
        $bad   = 'border-red-300 bg-red-50/40 focus:ring-red-500 focus:border-red-500';
        $cls   = fn (string $campo) => $input . ' ' . ($errors->has($campo) ? $bad : $ok);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-100">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Equipos</h1>
            <p class="text-sm text-gray-500 mt-1">Inventario de generadores y baterías.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" aria-label="Buscar equipos"
                    placeholder="Buscar por nombre o marca..."
                    class="w-full pl-9 pr-9 py-2 bg-gray-50/50 hover:bg-gray-50 focus:bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all">
                @if ($search !== '')
                    <button type="button" wire:click="$set('search', '')" aria-label="Limpiar búsqueda"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                @endif
            </div>
            <button wire:click="crear" type="button"
                class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all flex-shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden sm:inline">Nuevo equipo</span>
                <span class="sm:hidden">Nuevo</span>
            </button>
        </div>
    </div>

    <div>
        <div class="hidden md:block bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-2xs">
            <table class="min-w-full text-sm text-left">
                <thead
                    class="text-xs uppercase tracking-wider text-gray-400 font-medium border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-3.5 px-4">Equipo</th>
                        <th scope="col" class="py-3.5 px-4">Tipo</th>
                        <th scope="col" class="py-3.5 px-4">Capacidad</th>
                        <th scope="col" class="py-3.5 px-4">Características</th>
                        <th scope="col" class="py-3.5 px-4">Precio</th>
                        <th scope="col" class="py-3.5 px-4">Estado</th>
                        <th scope="col" class="py-3.5 px-4 text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @forelse ($generadores as $g)
                        @php
                            $tipoKey = $g->tipo ?? 'generador';
                            $t       = $tipos[$tipoKey] ?? $tipos['generador'];
                            $esGen   = $tipoKey === 'generador';
                            $kw      = $num($g->capacidad_kw);
                            $kva     = $num($g->capacidad_kva);
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors" wire:key="gen-{{ $g->id }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if ($g->imagen)
                                        <img src="{{ asset('storage/' . $g->imagen) }}" alt="{{ $g->nombre }}" loading="lazy"
                                            class="w-10 h-10 object-cover rounded-lg border border-gray-100 flex-shrink-0">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-300 flex-shrink-0">
                                            <i class="fa-solid {{ $t['icon'] }} text-sm"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate max-w-[16rem]">{{ $g->nombre }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $g->marca ?: 'Sin marca' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $t['badge'] }}">
                                    <i class="fa-solid {{ $t['icon'] }} text-[10px]"></i>
                                    {{ $t['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if ($kw !== null || $kva !== null)
                                    <p class="font-medium text-gray-900">{{ $kw !== null ? $kw . ' kW' : '—' }}</p>
                                    @if ($kva !== null)
                                        <p class="text-xs text-gray-400">{{ $kva }} kVA</p>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($esGen && ($g->fases || $g->combustible))
                                    <div class="flex flex-wrap gap-1">
                                        @if ($g->fases)
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs text-gray-600">{{ $fasesLbl[$g->fases] ?? ucfirst($g->fases) }}</span>
                                        @endif
                                        @if ($g->combustible)
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs text-gray-600">{{ $combLbl[$g->combustible] ?? ucfirst($g->combustible) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900 tabular-nums">{{ $dinero($g->precio) }}</td>
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $g->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $g->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $g->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button wire:click="editar({{ $g->id }})" type="button" title="Editar"
                                    aria-label="Editar {{ $g->nombre }}"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button
                                    wire:click="$dispatch('confirmar-eliminar', { id: {{ $g->id }}, nombre: @js($g->nombre) })"
                                    type="button" title="Eliminar" aria-label="Eliminar {{ $g->nombre }}"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div
                                    class="mx-auto mb-3 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid {{ $search !== '' ? 'fa-magnifying-glass' : 'fa-box-open' }}"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $search !== '' ? 'Sin resultados' : 'Aún no hay equipos' }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $search !== '' ? 'No encontramos coincidencias para «' . $search . '». Prueba con otro término.' : 'Agrega tu primer generador o batería para empezar.' }}
                                </p>
                                @if ($search === '')
                                    <button wire:click="crear" type="button"
                                        class="mt-4 inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all">
                                        <i class="fa-solid fa-plus text-xs"></i> Agregar equipo
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:hidden">
            @forelse ($generadores as $g)
                @php
                    $tipoKey = $g->tipo ?? 'generador';
                    $t       = $tipos[$tipoKey] ?? $tipos['generador'];
                    $esGen   = $tipoKey === 'generador';
                    $kw      = $num($g->capacidad_kw);
                    $kva     = $num($g->capacidad_kva);
                @endphp
                <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-3 shadow-2xs"
                    wire:key="gen-mobile-{{ $g->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($g->imagen)
                                <img src="{{ asset('storage/' . $g->imagen) }}" alt="{{ $g->nombre }}" loading="lazy"
                                    class="w-12 h-12 object-cover rounded-xl border border-gray-100 flex-shrink-0">
                            @else
                                <div
                                    class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-300 flex-shrink-0">
                                    <i class="fa-solid {{ $t['icon'] }}"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-medium text-gray-900 text-sm leading-snug truncate">{{ $g->nombre }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $g->marca ?: 'Sin marca' }}</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $g->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $g->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $g->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $t['badge'] }}">
                            <i class="fa-solid {{ $t['icon'] }} text-[10px]"></i> {{ $t['label'] }}
                        </span>
                        @if ($esGen && $g->fases)
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs text-gray-600">{{ $fasesLbl[$g->fases] ?? ucfirst($g->fases) }}</span>
                        @endif
                        @if ($esGen && $g->combustible)
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs text-gray-600">{{ $combLbl[$g->combustible] ?? ucfirst($g->combustible) }}</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-2 border-y border-gray-50 text-xs">
                        <div>
                            <span class="text-gray-400 block">kW</span>
                            <span class="font-medium text-gray-800">{{ $kw ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">kVA</span>
                            <span class="font-medium text-gray-800">{{ $kva ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Precio</span>
                            <span class="font-medium text-gray-800 tabular-nums">{{ $dinero($g->precio) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1 pt-1">
                        <button wire:click="editar({{ $g->id }})" type="button" title="Editar"
                            aria-label="Editar {{ $g->nombre }}"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button
                            wire:click="$dispatch('confirmar-eliminar', { id: {{ $g->id }}, nombre: @js($g->nombre) })"
                            type="button" title="Eliminar" aria-label="Eliminar {{ $g->nombre }}"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white border border-gray-100 rounded-2xl">
                    <div
                        class="mx-auto mb-3 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                        <i class="fa-solid {{ $search !== '' ? 'fa-magnifying-glass' : 'fa-box-open' }}"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $search !== '' ? 'Sin resultados' : 'Aún no hay equipos' }}</p>
                    <p class="text-sm text-gray-500 mt-1 px-4">
                        {{ $search !== '' ? 'Prueba con otro término de búsqueda.' : 'Agrega tu primer generador o batería para empezar.' }}
                    </p>
                </div>
            @endforelse
        </div>

        @if ($generadores->hasPages())
            <div class="mt-4">
                {{ $generadores->links() }}
            </div>
        @endif
    </div>

    {{-- ================= Modal crear/editar ================= --}}
    @if ($modalForm)
        <div x-data="{ tipo: $wire.entangle('tipo') }" x-init="$nextTick(() => $refs.nombre?.focus())"
            @keydown.escape.window="$wire.cerrarModal()"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-3 sm:p-6 overflow-y-auto"
            wire:click.self="cerrarModal" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden my-auto border border-gray-100 flex flex-col max-h-[90vh]">
                <div
                    class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 flex-shrink-0">
                    <div>
                        <h2 id="modal-titulo" class="text-base font-semibold text-gray-900">
                            {{ $equipoId ? 'Editar equipo' : 'Nuevo equipo' }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5"
                            x-text="tipo === 'bateria' ? 'Registra la batería y su capacidad.' : 'Registra la planta eléctrica y sus especificaciones.'">
                            {{ $tipo === 'bateria' ? 'Registra la batería y su capacidad.' : 'Registra la planta eléctrica y sus especificaciones.' }}
                        </p>
                    </div>
                    <button wire:click="cerrarModal" type="button" aria-label="Cerrar"
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-200/60 transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form wire:submit="guardar" class="flex flex-col flex-1 overflow-hidden" novalidate>
                    <div class="p-6 overflow-y-auto space-y-6 flex-1">
                        <fieldset>
                            <legend class="{{ $lbl }}">Tipo de equipo <span class="text-red-500" aria-hidden="true">*</span></legend>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($tipos as $clave => $t)
                                    <label class="cursor-pointer" wire:key="tipo-{{ $clave }}">
                                        <input type="radio" name="tipo" value="{{ $clave }}" x-model="tipo"
                                            class="peer sr-only">
                                        <div
                                            class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 transition-all hover:border-gray-300 peer-checked:border-gray-900 peer-checked:bg-gray-50 peer-checked:ring-1 peer-checked:ring-gray-900 peer-focus-visible:ring-2 peer-focus-visible:ring-gray-900 peer-focus-visible:ring-offset-2">
                                            <span
                                                class="w-9 h-9 rounded-lg flex items-center justify-center ring-1 ring-inset {{ $t['badge'] }}">
                                                <i class="fa-solid {{ $t['icon'] }} text-sm"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-medium text-gray-900">{{ $t['label'] }}</span>
                                                <span class="block text-xs text-gray-500">
                                                    {{ $clave === 'generador' ? 'Plantas eléctricas' : 'Almacenamiento de energía' }}
                                                </span>
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('tipo')
                                <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <section aria-labelledby="sec-general" class="space-y-3">
                            <h3 id="sec-general" class="{{ $sec }}">Información general</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label for="nombre" class="{{ $lbl }}">Nombre <span class="text-red-500" aria-hidden="true">*</span></label>
                                    <input id="nombre" x-ref="nombre" type="text" wire:model="nombre"
                                        :placeholder="tipo === 'bateria' ? 'Ej. Batería de litio 48V 100Ah' : 'Ej. Generador 10kVA - Gasolina'"
                                        placeholder="Ej. Generador 10kVA - Gasolina" autocomplete="off"
                                        @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
                                        class="{{ $cls('nombre') }}">
                                    @error('nombre')
                                        <p id="nombre-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="marca" class="{{ $lbl }}">Marca <span class="text-gray-400 font-normal">(opcional)</span></label>
                                    <input id="marca" type="text" wire:model="marca"
                                        :placeholder="tipo === 'bateria' ? 'Ej. Pylontech, Victron' : 'Ej. Cummins, Caterpillar'"
                                        placeholder="Ej. Cummins, Caterpillar" autocomplete="off"
                                        @error('marca') aria-invalid="true" aria-describedby="marca-error" @enderror
                                        class="{{ $cls('marca') }}">
                                    @error('marca')
                                        <p id="marca-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                            <div class="md:col-span-7 space-y-6">
                                <section aria-labelledby="sec-specs" class="space-y-3">
                                    <h3 id="sec-specs" class="{{ $sec }}">Especificaciones técnicas</h3>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="capacidad_kva" class="{{ $lbl }}">Capacidad kVA <span class="text-gray-400 font-normal">(opcional)</span></label>
                                            <div class="relative">
                                                <input id="capacidad_kva" type="number" step="0.01" min="0" inputmode="decimal"
                                                    wire:model="capacidad_kva" placeholder="0.00"
                                                    @error('capacidad_kva') aria-invalid="true" aria-describedby="kva-error" @enderror
                                                    class="{{ $cls('capacidad_kva') }} pr-12">
                                                <span class="pointer-events-none absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400">kVA</span>
                                            </div>
                                            @error('capacidad_kva')
                                                <p id="kva-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="capacidad_kw" class="{{ $lbl }}">
                                                Capacidad kW
                                                <span x-show="tipo === 'generador'" class="text-red-500" aria-hidden="true">*</span>
                                                <span x-show="tipo !== 'generador'" x-cloak class="text-gray-400 font-normal">(opcional)</span>
                                            </label>
                                            <div class="relative">
                                                <input id="capacidad_kw" type="number" step="0.01" min="0" inputmode="decimal"
                                                    wire:model="capacidad_kw" placeholder="0.00"
                                                    @error('capacidad_kw') aria-invalid="true" aria-describedby="kw-error" @enderror
                                                    class="{{ $cls('capacidad_kw') }} pr-10">
                                                <span class="pointer-events-none absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400">kW</span>
                                            </div>
                                            @error('capacidad_kw')
                                                <p id="kw-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div x-show="tipo === 'generador'" x-transition.opacity.duration.150ms
                                            class="contents">
                                            <div>
                                                <label for="fases" class="{{ $lbl }}">Fases <span class="text-red-500" aria-hidden="true">*</span></label>
                                                <select id="fases" wire:model="fases"
                                                    @error('fases') aria-invalid="true" aria-describedby="fases-error" @enderror
                                                    class="{{ $cls('fases') }}">
                                                    <option value="">Seleccionar…</option>
                                                    <option value="monofasico">Monofásico</option>
                                                    <option value="trifasico">Trifásico</option>
                                                </select>
                                                @error('fases')
                                                    <p id="fases-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="combustible" class="{{ $lbl }}">Combustible <span class="text-red-500" aria-hidden="true">*</span></label>
                                                <select id="combustible" wire:model="combustible"
                                                    @error('combustible') aria-invalid="true" aria-describedby="comb-error" @enderror
                                                    class="{{ $cls('combustible') }}">
                                                    <option value="gasolina">Gasolina</option>
                                                    <option value="diesel">Diésel</option>
                                                    <option value="gas">Gas</option>
                                                </select>
                                                @error('combustible')
                                                    <p id="comb-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <p x-show="tipo === 'bateria'" x-cloak
                                            class="col-span-2 flex items-start gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs text-sky-700">
                                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                                            Las baterías no requieren fases ni combustible.
                                        </p>
                                    </div>
                                </section>

                                <section aria-labelledby="sec-precio" class="space-y-3">
                                    <h3 id="sec-precio" class="{{ $sec }}">Precio</h3>
                                    <div class="max-w-[12rem]">
                                        <label for="precio" class="{{ $lbl }}">Precio de venta <span class="text-gray-400 font-normal">(opcional)</span></label>
                                        <div class="relative">
                                            <span class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">$</span>
                                            <input id="precio" type="number" step="0.01" min="0" inputmode="decimal"
                                                wire:model="precio" placeholder="0.00"
                                                @error('precio') aria-invalid="true" aria-describedby="precio-error" @enderror
                                                class="{{ $cls('precio') }} pl-7">
                                        </div>
                                        @error('precio')
                                            <p id="precio-error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </section>
                            </div>

                            <div class="md:col-span-5 space-y-6">
                                <section aria-labelledby="sec-imagen" class="space-y-3">
                                    <h3 id="sec-imagen" class="{{ $sec }}">Fotografía</h3>
                                    <div>
                                        <div wire:ignore x-data="{
                                            init() {
                                                Dropzone.autoDiscover = false;
                                                const wire = this.$wire;
                                                const actual = @js($imagenActual ? asset('storage/' . $imagenActual) : null);

                                                const dz = new Dropzone(this.$refs.dz, {
                                                    url: '#',
                                                    autoProcessQueue: false,
                                                    acceptedFiles: 'image/*',
                                                    addRemoveLinks: true,
                                                    dictDefaultMessage: 'Arrastra o haz clic para subir imagen',
                                                    dictRemoveFile: 'Quitar',
                                                    init() {
                                                        this.on('addedfile', (file) => {
                                                            if (file.isMock) return;
                                                            if (!file.type.startsWith('image/')) {
                                                                this.removeFile(file);
                                                                return;
                                                            }
                                                            this.files.filter(f => f !== file).forEach(f => this.removeFile(f));
                                                            wire.upload('imagen', file);
                                                        });
                                                        this.on('removedfile', (file) => {
                                                            if (!file.isMock) wire.set('imagen', null, false);
                                                        });
                                                    }
                                                });

                                                if (actual) {
                                                    const mock = { name: 'Actual', size: 0, isMock: true };
                                                    dz.emit('addedfile', mock);
                                                    dz.emit('thumbnail', mock, actual);
                                                    dz.emit('complete', mock);
                                                    dz.files.push(mock);
                                                }
                                            }
                                        }">
                                            <div x-ref="dz"
                                                class="dropzone border border-dashed border-gray-200 hover:border-gray-300 rounded-xl bg-gray-50/30 text-xs p-2 min-h-[140px] flex items-center justify-center transition-colors">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1.5">JPG, PNG o WebP · máximo 2 MB.</p>
                                        <div wire:loading wire:target="imagen"
                                            class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 font-medium">
                                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Subiendo imagen...
                                        </div>
                                        @error('imagen')
                                            <p class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </section>

                                <section aria-labelledby="sec-estado" class="space-y-3">
                                    <h3 id="sec-estado" class="{{ $sec }}">Disponibilidad</h3>
                                    <label
                                        class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 p-3 cursor-pointer hover:border-gray-300 transition-colors">
                                        <span>
                                            <span class="block text-sm font-medium text-gray-900">Equipo activo</span>
                                            <span class="block text-xs text-gray-500">Disponible en el sistema.</span>
                                        </span>
                                        <span class="relative inline-flex flex-shrink-0 mt-0.5">
                                            <input type="checkbox" wire:model="activo" class="peer sr-only">
                                            <span
                                                class="block w-10 h-6 rounded-full bg-gray-200 transition-colors peer-checked:bg-emerald-500 peer-focus-visible:ring-2 peer-focus-visible:ring-gray-900 peer-focus-visible:ring-offset-2"></span>
                                            <span
                                                class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></span>
                                        </span>
                                    </label>
                                </section>
                            </div>
                        </div>

                        @php
                            $descripcionInicial = (string) ($descripcion ?? '');
                            if ($descripcionInicial !== '' && !preg_match('/<[a-z][\s\S]*>/i', $descripcionInicial)) {
                                $descripcionInicial = nl2br(e($descripcionInicial));
                            }
                        @endphp
                        <section aria-labelledby="sec-desc" class="space-y-3">
                            <h3 id="sec-desc" class="{{ $sec }}">Descripción <span class="normal-case tracking-normal font-normal">(opcional)</span></h3>

                            <div wire:ignore x-data="{
                                init() {
                                    const wire = this.$wire;

                                    const quill = new Quill(this.$refs.editor, {
                                        theme: 'snow',
                                        placeholder: 'Detalles adicionales, accesorios incluidos, garantía…',
                                        modules: {
                                            toolbar: [
                                                ['bold', 'italic', 'underline'],
                                                [{ list: 'ordered' }, { list: 'bullet' }],
                                                ['clean'],
                                            ],
                                        },
                                    });

                                    const inicial = @js($descripcionInicial);
                                    if (inicial) {
                                        quill.clipboard.dangerouslyPasteHTML(inicial, 'silent');
                                    }

                                    quill.on('text-change', () => {
                                        const vacio = quill.getText().trim() === '';
                                        wire.set('descripcion', vacio ? '' : quill.getSemanticHTML(), false);
                                    });
                                }
                            }">
                                <div x-ref="editor" class="bg-white text-sm" style="min-height: 120px;"></div>
                            </div>

                            @error('descripcion')
                                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </section>
                    </div>

                    <div
                        class="flex items-center gap-2.5 px-6 py-3.5 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
                        <p class="hidden sm:block mr-auto text-xs text-gray-400">
                            <span class="text-red-500">*</span> Campo obligatorio
                        </p>
                        <div class="flex w-full sm:w-auto flex-col-reverse sm:flex-row items-stretch sm:items-center gap-2.5">
                            <button type="button" wire:click="cerrarModal"
                                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="guardar,imagen"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg wire:loading.inline-block wire:target="guardar" class="animate-spin w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="guardar">{{ $equipoId ? 'Guardar cambios' : 'Crear equipo' }}</span>
                                <span wire:loading.inline wire:target="guardar">Guardando…</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
                const {
                    id,
                    nombre
                } = Array.isArray(e) ? e[0] : e;

                Swal.fire({
                    title: '¿Eliminar equipo?',
                    html: `Se eliminará <strong class="text-gray-900">${nombre}</strong> junto con su imagen.<br>Esta acción no se puede deshacer.`,
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
