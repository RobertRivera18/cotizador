@php
$links = [
    // GENERAL
    [
        'section' => 'General',
        'items' => [
            [
                'name' => 'Dashboard',
                'url' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'fas fa-gauge-simple-high',
                'can' => ['Acceso al Dashboard'],
            ],
            [
                'name' => 'Generadores',
                'url' => route('admin.generadores'),
                'active' => request()->routeIs('admin.generadores'),
                'icon' => 'fas fa-bolt',
                'can' => ['Gestion de generadores'],
            ],
            [
                'name' => 'Cotizaciones',
                'url' => route('admin.cotizaciones.index'),
                'active' => request()->routeIs('admin.cotizaciones.*'),
                'icon' => 'fas fa-file-invoice-dollar',
                // Debe coincidir con el permiso de la ruta del listado
                'can' => ['Ver cotizaciones'],
            ],
        ],
    ],

    // ADMINISTRACIÓN Y AUDITORÍA
    [
        'section' => 'Organización & Control',
        'items' => [
            [
                'name' => 'Gestión de Accesos',
                'icon' => 'fas fa-shield-halved',
                'submenu' => [
                    [
                        'name' => 'Roles',
                        'url' => route('admin.roles.index'),
                        'active' => request()->routeIs('admin.roles.*'),
                        'icon' => 'fas fa-user-tag',
                        'can' => ['Gestion de roles'],
                    ],
                    [
                        'name' => 'Permisos',
                        'url' => route('admin.permissions.index'),
                        'active' => request()->routeIs('admin.permissions.*'),
                        'icon' => 'fas fa-key',
                        'can' => ['Gestion de permisos'],
                    ],
                    // Solo aparece cuando exista la ruta de usuarios (evita error si aún no está creada)
                    ...(\Illuminate\Support\Facades\Route::has('admin.users.index') ? [[
                        'name' => 'Usuarios',
                        'url' => route('admin.users.index'),
                        'active' => request()->routeIs('admin.users.*'),
                        'icon' => 'fas fa-users',
                        'can' => ['Gestion de Usuarios'],
                    ]] : []),
                ],
            ],
        ],
    ],
];

// ---- Filtro por permisos: deja solo lo que el usuario puede usar ----
$usuarioActual = auth()->user();
$puede = fn (array $permisos) => empty($permisos) || ($usuarioActual && $usuarioActual->canAny($permisos));

$links = collect($links)
    ->map(function ($group) use ($puede) {
        $group['items'] = collect($group['items'])
            ->map(function ($item) use ($puede) {
                if (isset($item['submenu'])) {
                    $item['submenu'] = collect($item['submenu'])
                        ->filter(fn ($sub) => $puede($sub['can'] ?? []))
                        ->values()
                        ->all();

                    // Si no queda ningún subítem permitido, se oculta el menú padre
                    return count($item['submenu']) > 0 ? $item : null;
                }

                return $puede($item['can'] ?? []) ? $item : null;
            })
            ->filter()
            ->values()
            ->all();

        return $group;
    })
    // Si una sección se queda sin ítems, se oculta también su título
    ->filter(fn ($group) => count($group['items']) > 0)
    ->values()
    ->all();
@endphp

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full bg-white border-r border-gray-200/80 sm:translate-x-0 dark:bg-gray-900 dark:border-gray-800"
    aria-label="Sidebar" :class="{ '-translate-x-full': !open, 'transform-none': open }">

    <div class="h-full px-3 py-4 overflow-y-auto bg-white dark:bg-gray-900 space-y-6 custom-scrollbar">
        @foreach ($links as $group)
        <div>
            <!-- Encabezado de Sección TailAdmin -->
            <h3 class="mb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                {{ $group['section'] }}
            </h3>

            <ul class="space-y-1 font-medium">
                @foreach ($group['items'] as $link)
                {{-- Ítem con Submenú --}}
                @if (isset($link['submenu']))
                @php
                $isSubmenuActive = collect($link['submenu'])->contains(fn($sub) => $sub['active']);
                @endphp

                <li x-data="{ openMenu: {{ $isSubmenuActive ? 'true' : 'false' }} }">
                    <button @click="openMenu = !openMenu"
                        class="flex items-center w-full p-2.5 text-xs font-medium rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/60 hover:text-gray-900 dark:hover:text-white transition-all group {{ $isSubmenuActive ? 'bg-gray-50 dark:bg-gray-800/40 text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">

                        <i
                            class="{{ $link['icon'] }} w-5 h-5 flex items-center justify-center text-sm text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300 transition-colors {{ $isSubmenuActive ? 'text-indigo-600 dark:text-indigo-400' : '' }}"></i>
                        <span class="flex-1 ms-3 text-left truncate">{{ $link['name'] }}</span>

                        <svg class="w-3.5 h-3.5 transform transition-transform duration-200 text-gray-400"
                            :class="{ 'rotate-180': openMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Submenú Desplegable con Línea Guía -->
                    <ul x-show="openMenu" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 pl-4 ms-3.5 space-y-1 border-l-2 border-gray-100 dark:border-gray-800">

                        @foreach ($link['submenu'] as $sub)
                        <li>
                            <a href="{{ $sub['url'] }}"
                                class="flex items-center w-full p-2 text-xs font-medium transition-all rounded-lg {{ $sub['active'] ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100/60 dark:hover:bg-gray-800/40 hover:text-gray-900 dark:hover:text-white' }}">
                                <i
                                    class="{{ $sub['icon'] }} w-4 text-center me-2 text-[11px] {{ $sub['active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}"></i>
                                <span class="truncate">{{ $sub['name'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>

                {{-- Ítem Individual --}}
                @else
                <li>
                    <a href="{{ $link['url'] }}"
                        class="flex items-center p-2.5 text-xs font-medium rounded-xl transition-all group {{ $link['active'] ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/60 hover:text-gray-900 dark:hover:text-white' }}">
                        <i
                            class="{{ $link['icon'] }} w-5 h-5 flex items-center justify-center text-sm text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300 transition-colors {{ $link['active'] ? 'text-indigo-600 dark:text-indigo-400' : '' }}"></i>
                        <span class="ms-3 truncate">{{ $link['name'] }}</span>
                    </a>
                </li>
                @endif
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</aside>
