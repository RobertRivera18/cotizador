<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-100">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Generadores</h1>
            <p class="text-sm text-gray-500 mt-1">Inventario y control de plantas eléctricas.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..."
                    class="w-full pl-9 pr-4 py-2 bg-gray-50/50 hover:bg-gray-50 focus:bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all">
            </div>
            <button wire:click="crear"
                class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all flex-shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden sm:inline">Nuevo generador</span>
                <span class="sm:hidden">Nuevo</span>
            </button>
        </div>
    </div>

    {{-- Contenedor de datos (tarjetas en móvil / tabla en desktop) --}}
    <div>
        {{-- Tabla para desktop --}}
        <div class="hidden md:block bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-2xs">
            <table class="min-w-full text-sm text-left">
                <thead
                    class="text-xs uppercase tracking-wider text-gray-400 font-medium border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th class="py-3.5 px-4 w-16">Imagen</th>
                        <th class="py-3.5 px-4">Nombre</th>
                        <th class="py-3.5 px-4">kVA</th>
                        <th class="py-3.5 px-4">kW</th>
                        <th class="py-3.5 px-4">Combustible</th>
                        <th class="py-3.5 px-4">Precio</th>
                        <th class="py-3.5 px-4">Estado</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @forelse ($generadores as $g)
                    <tr class="hover:bg-gray-50/50 transition-colors" wire:key="gen-{{ $g->id }}">
                        <td class="py-3 px-4">
                            @if ($g->imagen)
                            <img src="{{ asset('storage/' . $g->imagen) }}"
                                class="w-10 h-10 object-cover rounded-lg border border-gray-100">
                            @else
                            <div
                                class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $g->nombre }}</td>
                        <td class="py-3 px-4">{{ $g->capacidad_kva }}</td>
                        <td class="py-3 px-4">{{ $g->capacidad_kw }}</td>
                        <td class="py-3 px-4 capitalize">{{ $g->combustible }}</td>
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $g->precio !== null ? '$' .
                            number_format($g->precio, 2) : '—' }}</td>
                        <td class="py-3 px-4">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $g->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ $g->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $g->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap space-x-2">
                            <button wire:click="editar({{ $g->id }})" title="Editar"
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button
                                wire:click="$dispatch('confirmar-eliminar', { id: {{ $g->id }}, nombre: @js($g->nombre) })"
                                title="Eliminar"
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400">
                            <p class="text-sm">No hay generadores registrados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tarjetas para móvil --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:hidden">
            @forelse ($generadores as $g)
            <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-3 shadow-2xs"
                wire:key="gen-mobile-{{ $g->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($g->imagen)
                        <img src="{{ asset('storage/' . $g->imagen) }}"
                            class="w-12 h-12 object-cover rounded-xl border border-gray-100 flex-shrink-0">
                        @else
                        <div
                            class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-300 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                        <div>
                            <h3 class="font-medium text-gray-900 text-sm leading-snug">{{ $g->nombre }}</h3>
                            <p class="text-xs text-gray-400 capitalize mt-0.5">{{ $g->combustible }}</p>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $g->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full {{ $g->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $g->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 py-2 border-y border-gray-50 text-xs">
                    <div>
                        <span class="text-gray-400 block">kVA</span>
                        <span class="font-medium text-gray-800">{{ $g->capacidad_kva }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">kW</span>
                        <span class="font-medium text-gray-800">{{ $g->capacidad_kw }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Precio</span>
                        <span class="font-medium text-gray-800">{{ $g->precio !== null ? '$' . number_format($g->precio,
                            2) : '—' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-1 pt-1">
                    <button wire:click="editar({{ $g->id }})" title="Editar"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button
                        wire:click="$dispatch('confirmar-eliminar', { id: {{ $g->id }}, nombre: @js($g->nombre) })"
                        title="Eliminar"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 bg-white border border-gray-100 rounded-2xl">
                <p class="text-sm">No hay generadores registrados.</p>
            </div>
            @endforelse
        </div>

        @if ($generadores->hasPages())
        <div class="mt-4">
            {{ $generadores->links() }}
        </div>
        @endif
    </div>

    {{-- Modal crear/editar --}}
    @if ($modalForm)
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-3 sm:p-6 overflow-y-auto"
        wire:click.self="cerrarModal">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden my-auto border border-gray-100 flex flex-col max-h-[90vh]">

            {{-- Header fijo --}}
            <div
                class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 flex-shrink-0">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $generadorId ? 'Editar generador' : 'Nuevo
                        generador' }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Ingresa las especificaciones de la planta eléctrica.</p>
                </div>
                <button wire:click="cerrarModal" type="button"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-200/60 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            {{-- Formulario con scroll interno --}}
            <form wire:submit="guardar" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto space-y-5 flex-1">

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre del generador</label>
                        <input type="text" wire:model="nombre" placeholder="Ej. Generador 10kVA - Gasolina"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all">
                        @error('nombre') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Grid 2 columnas: datos técnicos y foto --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                        {{-- Columna izquierda: especificaciones --}}
                        <div class="md:col-span-7 space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Capacidad
                                        (kVA)</label>
                                    <input type="number" step="0.01" wire:model="capacidad_kva" placeholder="0.00"
                                        class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                                    @error('capacidad_kva') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Capacidad (kW)</label>
                                    <input type="number" step="0.01" wire:model="capacidad_kw" placeholder="0.00"
                                        class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                                    @error('capacidad_kw') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Combustible</label>
                                    <select wire:model="combustible"
                                        class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                                        <option value="gasolina">Gasolina</option>
                                        <option value="diesel">Diésel</option>
                                        <option value="gas">Gas</option>
                                    </select>
                                    @error('combustible') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Precio <span
                                            class="text-gray-400 font-normal">(opcional)</span></label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">$</span>
                                        <input type="number" step="0.01" wire:model="precio" placeholder="0.00"
                                            class="w-full bg-gray-50/50 pl-7 pr-3 border border-gray-200 rounded-lg py-2 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                                    </div>
                                    @error('precio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Descripción con editor Quill --}}
                            @php
                                // Descripciones antiguas guardadas como texto plano: se convierten a HTML para el editor
                                $descripcionInicial = (string) ($descripcion ?? '');
                                if ($descripcionInicial !== '' && ! preg_match('/<[a-z][\s\S]*>/i', $descripcionInicial)) {
                                    $descripcionInicial = nl2br(e($descripcionInicial));
                                }
                            @endphp
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Descripción o
                                    detalles</label>

                                <div wire:ignore x-data="{
                                    init() {
                                        const wire = this.$wire;

                                        const quill = new Quill(this.$refs.editor, {
                                            theme: 'snow',
                                            placeholder: 'Especificaciones adicionales...',
                                            modules: {
                                                toolbar: [
                                                    ['bold', 'italic', 'underline'],
                                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                                    ['clean'],
                                                ],
                                            },
                                        });

                                        // Contenido inicial (al editar). 'silent' evita marcarlo como cambio del usuario
                                        const inicial = @js($descripcionInicial);
                                        if (inicial) {
                                            quill.clipboard.dangerouslyPasteHTML(inicial, 'silent');
                                        }

                                        // Sincroniza con Livewire sin lanzar una petición en cada tecla
                                        quill.on('text-change', () => {
                                            const vacio = quill.getText().trim() === '';
                                            wire.set('descripcion', vacio ? '' : quill.getSemanticHTML(), false);
                                        });
                                    }
                                }">
                                    <div x-ref="editor" class="bg-white text-sm" style="min-height: 120px;"></div>
                                </div>

                                @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Columna derecha: carga de imagen --}}
                        <div class="md:col-span-5 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Fotografía del
                                    equipo</label>
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
                                        class="dropzone border border-dashed border-gray-200 rounded-xl bg-gray-50/30 text-xs p-2 min-h-[140px] flex items-center justify-center">
                                    </div>
                                </div>
                                <div wire:loading wire:target="imagen"
                                    class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 font-medium">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Subiendo imagen...
                                </div>
                                @error('imagen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-2">
                                <label
                                    class="inline-flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer select-none">
                                    <input type="checkbox" wire:model="activo"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900 w-4 h-4">
                                    Disponible y activo en sistema
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer fijo con botones siempre visibles --}}
                <div
                    class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
                    <button type="button" wire:click="cerrarModal"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium transition-all shadow-sm disabled:opacity-50"
                        wire:loading.attr="disabled">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- SweetAlert: notificaciones y confirmación de eliminación --}}
    @script
    <script>
        // Notificación discreta de éxito (esquina superior derecha)
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

        // Confirmación antes de eliminar
        $wire.on('confirmar-eliminar', (e) => {
            const { id, nombre } = Array.isArray(e) ? e[0] : e;

            Swal.fire({
                title: '¿Eliminar generador?',
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
