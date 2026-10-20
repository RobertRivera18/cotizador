<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Tarjeta de Bienvenida -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/60 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="relative">
                <img class="w-16 h-16 rounded-2xl object-cover ring-4 ring-indigo-500/10 dark:ring-indigo-500/20 shadow-sm"
                     src="{{ Auth::user()->profile_photo_url }}"
                     alt="{{ Auth::user()->name }}">
                <span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full" title="Conectado"></span>
            </div>

            <div class="flex-1 text-center sm:text-left w-full">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                            ¡Bienvenido de nuevo, {{ Auth::user()->name }}!
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Panel general de Cotizaciones.
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        Sesión activa
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center text-xs font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 transition-colors gap-1.5">
                            <i class="fas fa-right-from-bracket text-[11px]"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Marca / Logo DISEIN -->
        <div class="bg-white dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/60 rounded-2xl p-5 shadow-sm flex items-center justify-center text-center relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition-all"></div>

            <div class="flex flex-col items-center justify-center gap-1.5 z-10">
                <h3 class="text-xs font-extrabold text-gray-400 dark:text-gray-500 tracking-widest uppercase">Sistema</h3>
                <img src="{{ asset('img/DISEIN.png') }}" alt="DISEIN" class="h-12 w-auto object-contain my-1">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Gestión de Equipos Fotovoltaicos y Proformas</span>
            </div>
        </div>
    </div>
</x-admin-layout>
