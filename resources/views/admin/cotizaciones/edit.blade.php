<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'url' => route('admin.dashboard'),
    ],
    [
        'name' => 'Cotizaciones',
        'url' => route('admin.cotizaciones.index'),
    ],
    [
        'name' => 'Editar cotización #' . $cotizacion->id,
    ],
]">
    <livewire:cotizacion-edit :cotizacion="$cotizacion" />
</x-admin-layout>
