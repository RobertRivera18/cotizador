<div class="mx-auto max-w-6xl p-4 sm:p-6 lg:p-8">

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Equipos eléctricos</h1>
            <p class="mt-1 text-sm text-gray-500">Crea equipos y asigne parámetros técnicos para los entornos del
                configurador.</p>
        </div>
        <button wire:click="crear" type="button"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo equipo
        </button>
    </div>

    {{-- Notificación Flash con animación y mejor icono --}}
    @if (session('ok'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm transition-all">
            <div class="flex items-center gap-2.5">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('ok') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    {{-- Barra de herramientas (Buscador y filtros opcionales) --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..."
                class="w-full rounded-xl border-gray-300 pl-9 pr-4 text-sm shadow-sm transition-colors focus:border-orange-500 focus:ring-orange-500">
        </div>
    </div>

    {{-- Tabla de datos --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Equipo</th>
                        <th scope="col" class="px-6 py-3.5">Nominal</th>
                        <th scope="col" class="px-6 py-3.5">Arranque</th>
                        <th scope="col" class="px-6 py-3.5">Motor</th>
                        <th scope="col" class="px-6 py-3.5">Entornos</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($equipos as $equipo)
                        <tr wire:key="equipo-{{ $equipo->id }}" class="transition-colors hover:bg-gray-50/60">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-base shadow-sm">{{ $equipo->icono ?: '⚡' }}</span>
                                    <span class="font-semibold text-gray-900">{{ $equipo->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span
                                    class="font-medium text-gray-900">{{ number_format($equipo->potencia_nominal_w) }}</span>
                                <span class="text-xs text-gray-400">W</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                @if ($equipo->potencia_arranque_w)
                                    <span
                                        class="font-medium text-gray-900">{{ number_format($equipo->potencia_arranque_w) }}</span>
                                    <span class="text-xs text-gray-400">W</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($equipo->es_motor)
                                    <span
                                        class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800 border border-amber-200/60">Sí</span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @forelse ($equipo->entornos as $e)
                                        <span
                                            class="inline-flex items-center rounded-lg bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-700 border border-orange-100">{{ $e->nombre }}</span>
                                    @empty
                                        <span class="text-xs italic text-gray-400">Sin asignar</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <button wire:click="editar({{ $equipo->id }})" type="button"
                                        class="inline-flex items-center gap-1.5 font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button wire:click="eliminar({{ $equipo->id }})"
                                        wire:confirm="¿Estás seguro de eliminar '{{ $equipo->nombre }}'? Se quitará automáticamente de todos los entornos."
                                        type="button"
                                        class="inline-flex items-center gap-1.5 font-medium text-rose-600 hover:text-rose-900 transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center justify-center">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No se encontraron equipos</p>
                                    <p class="mt-1 text-xs text-gray-500">Intenta buscando con otro término o crea un
                                        nuevo equipo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $equipos->links() }}</div>

    {{-- Modal Crear / Editar Mejorado (Responsive Fullscreen en móviles) --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4"
            wire:keydown.escape.window="cerrar">
            <div class="w-full h-full sm:h-auto sm:max-h-[90vh] sm:max-w-3xl flex flex-col bg-white sm:rounded-2xl shadow-2xl overflow-hidden"
                wire:click.stop>

                {{-- Cabecera del Modal --}}
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 bg-gray-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ $equipoId ? 'Editar equipo eléctrico' : 'Nuevo equipo eléctrico' }}
                        </h2>
                        <p class="text-xs text-gray-500">Configura las especificaciones del elemento eléctrico.</p>
                    </div>
                    <button type="button" wire:click="cerrar"
                        class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="guardar" class="flex-1 overflow-y-auto">
                    <div class="space-y-6 px-6 py-6">

                        {{-- Datos Principales --}}
                        <div class="grid gap-5 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Nombre del
                                    equipo</label>
                                <input type="text" wire:model="nombre"
                                    placeholder="Ej: Aire Acondicionado 18k BTU"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                @error('nombre')
                                    <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Ícono (Emoji)</label>
                                <input type="text" wire:model="icono" maxlength="10" placeholder="❄️"
                                    class="w-full rounded-xl border-gray-300 text-sm text-center shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                @error('icono')
                                    <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Potencia nominal
                                    (W)</label>
                                <input type="number" min="1" wire:model="potencia_nominal_w"
                                    placeholder="Ej: 1500"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                @error('potencia_nominal_w')
                                    <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2 flex items-end">
                                <label class="relative flex items-center gap-3 cursor-pointer py-2.5">
                                    <input type="checkbox" wire:model.live="es_motor"
                                        class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-sm font-semibold text-gray-700">¿Tiene motor?</span>
                                </label>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Arranque (W)</label>
                                <input type="number" min="1" wire:model="potencia_arranque_w"
                                    @disabled(!$es_motor)
                                    placeholder="{{ $es_motor ? 'Ej: 4500' : 'No aplica' }}"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 disabled:bg-gray-100 disabled:text-gray-400">
                                @error('potencia_arranque_w')
                                    <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Sección Entornos --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-bold text-gray-900">Asignación a entornos</h3>
                            <p class="mt-0.5 text-xs text-gray-500">
                                Define en qué espacios estará disponible este equipo, su cantidad inicial y su orden
                                predeterminado.
                            </p>

                            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead
                                        class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3">Entorno</th>
                                            <th class="px-4 py-3">Cantidad</th>
                                            <th class="px-4 py-3">Seleccionado</th>
                                            <th class="px-4 py-3">Orden</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($entornos as $entorno)
                                            <tr wire:key="asig-{{ $entorno->id }}"
                                                class="transition-colors {{ $asignaciones[$entorno->id]['activo'] ?? false ? 'bg-orange-50/40' : '' }}">
                                                <td class="px-4 py-3">
                                                    <label
                                                        class="flex items-center gap-2.5 font-medium text-gray-900 cursor-pointer">
                                                        <input type="checkbox"
                                                            wire:model.live="asignaciones.{{ $entorno->id }}.activo"
                                                            class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                                        {{ $entorno->nombre }}
                                                    </label>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" min="0"
                                                        wire:model="asignaciones.{{ $entorno->id }}.cantidad"
                                                        @disabled(!($asignaciones[$entorno->id]['activo'] ?? false))
                                                        class="w-20 rounded-lg border-gray-300 text-sm shadow-sm disabled:bg-gray-100 disabled:text-gray-400">
                                                    @error("asignaciones.{$entorno->id}.cantidad")
                                                        <p class="mt-1 text-xs font-medium text-rose-600">
                                                            {{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="checkbox"
                                                        wire:model="asignaciones.{{ $entorno->id }}.seleccionado"
                                                        @disabled(!($asignaciones[$entorno->id]['activo'] ?? false))
                                                        class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500 disabled:opacity-40">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" min="0"
                                                        wire:model="asignaciones.{{ $entorno->id }}.orden"
                                                        @disabled(!($asignaciones[$entorno->id]['activo'] ?? false))
                                                        class="w-20 rounded-lg border-gray-300 text-sm shadow-sm disabled:bg-gray-100 disabled:text-gray-400">
                                                    @error("asignaciones.{$entorno->id}.orden")
                                                        <p class="mt-1 text-xs font-medium text-rose-600">
                                                            {{ $message }}</p>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    {{-- Footer del Modal --}}
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <button type="button" wire:click="cerrar"
                            class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all disabled:opacity-50"
                            wire:loading.attr="disabled" wire:target="guardar">
                            <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                            <span wire:loading wire:target="guardar" class="inline-flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
