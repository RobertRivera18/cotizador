<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['name' => 'Cotizaciones', 'url' => route('admin.cotizaciones.index')],
    ['name' => 'Crear Cotización'],
]">
    @livewire('cotizacion-create')
</x-admin-layout>
