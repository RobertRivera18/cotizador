<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Cotizaciones'],
]">

    @livewire('cotizaciones-all')

</x-admin-layout>
