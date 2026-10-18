<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Cotizaciones',
        'route' => route('admin.cotizaciones.index'),
    ],
    [
        'name' => 'Editar cotización #' . $cotizacion->id,
    ],
]">
    <livewire:cotizacion-edit :cotizacion="$cotizacion" />
</x-admin-layout>
