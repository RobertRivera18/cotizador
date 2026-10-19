<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-slate-100 font-sans antialiased px-4 sm:px-6 lg:px-8 relative">

        {{-- Patrón sutil de fondo tipo cuadrícula técnica --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#cbd5e140_1px,transparent_1px),linear-gradient(to_bottom,#cbd5e140_1px,transparent_1px)] bg-[size:20px_20px] pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">

            {{-- Encabezado con Logos Corporativos (DISEIN + Grupo BM) --}}
            <div class="text-center mb-8">
                <div class="inline-flex justify-center items-center p-4 bg-white border border-slate-200/80 rounded-2xl shadow-sm mb-3">
                    <x-application-logo />
                </div>
                <h1 class="text-xs font-mono text-slate-500 font-semibold tracking-widest uppercase">Suministros Eléctricos Industriales</h1>
            </div>

            {{-- Tarjeta Principal de Autenticación --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl p-8 shadow-xl shadow-slate-200/50">

                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">Acceso al Sistema</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Módulo interno de control y cotizaciones.</p>
                </div>

                {{-- Errores de Validación --}}
                <x-validation-errors class="mb-5 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-xs font-mono" />

                @session('status')
                    <div class="mb-5 font-mono text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 p-3 rounded-lg">
                        {{ $value }}
                    </div>
                @endsession

                {{-- Formulario --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-label for="email" value="{{ __('Correo Electrónico') }}" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 block" />
                        <x-input id="email"
                            class="block w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-slate-900 text-sm placeholder-slate-400 transition-colors"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nombre@diseinelectric.com" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <x-label for="password" value="{{ __('Contraseña') }}" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block" />
                            @if (Route::has('password.request'))
                                <a class="text-xs text-amber-600 hover:text-amber-700 transition-colors font-medium" href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <x-input id="password"
                            class="block w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-slate-900 text-sm placeholder-slate-400 transition-colors"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••••" />
                    </div>

                    <div class="block pt-1">
                        <label for="remember_me" class="flex items-center cursor-pointer select-none">
                            <x-checkbox id="remember_me" name="remember" class="w-4 h-4 rounded bg-white border-slate-300 text-amber-500 focus:ring-amber-500/50" />
                            <span class="ms-2.5 text-xs text-slate-600 font-medium">{{ __('Mantener sesión iniciada') }}</span>
                        </label>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center py-2.5 px-4 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-md shadow-slate-900/10 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                            {{ __('Ingresar al Sistema') }}
                        </button>
                    </div>
                </form>

            </div>

            {{-- Pie de página corporativo --}}
            <div class="text-center mt-6 text-[11px] font-mono text-slate-400 space-y-1">
                <p>RUC: 0991307842001 &bull; Guayaquil, Ecuador</p>
                <p>&copy; {{ date('Y') }} DISEIN Cía. Ltda. — Grupo BM</p>
            </div>

        </div>
    </div>
</x-guest-layout>
