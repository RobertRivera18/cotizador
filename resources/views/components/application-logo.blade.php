<div {{ $attributes->merge(['class' => 'flex items-center justify-center gap-4']) }}>
    {{-- Logo DISEIN --}}
    @if(file_exists(public_path('img/DISEIN.png')))
        <img src="{{ asset('img/DISEIN.png') }}" alt="DISEIN" class="h-12 w-auto object-contain">
    @else
        <span class="text-xl font-black text-slate-900 tracking-wider">DISEIN</span>
    @endif

    {{-- Divisor sutil --}}
    <div class="h-8 w-px bg-slate-300"></div>

    {{-- Logo Grupo BM --}}
    @if(file_exists(public_path('img/grupobm.png')))
        <img src="{{ asset('img/grupobm.png') }}" alt="Grupo BM" class="h-10 w-auto object-contain opacity-90">
    @else
        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Grupo BM</span>
    @endif
</div>
