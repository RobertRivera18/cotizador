<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cotizacion;
use App\Models\Entorno;
use App\Models\EquipoElectrico;
use App\Models\Generador;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;


class Cotizador extends Component
{
    public int $step = 1;

    public ?int $entorno_id = null;
    public array $equipos = [];

    public string $busquedaEquipo = '';
    public array $prioridades = [];
    public array $filtroCombustible = [
        'gasolina' => false,
        'diesel' => false,
        'gas' => false,
    ];

    public bool $mostrarModalCotizacion = false;
    public string $nombre_cliente = '';
    public string $telefono = '';
    public string $email = '';
    public bool $cotizacionEnviada = false;
    public ?int $generadorSeleccionado = null;
    protected float $margenSeguridad = 0.25;
    protected float $factorPotencia = 0.8;

    public function mount(): void
    {
        $primerEntorno = Entorno::orderBy('orden')->first();
        if ($primerEntorno) {
            $this->seleccionarEntorno($primerEntorno->id);
        }
    }
    #[Computed]
    public function entornos(): Collection
    {
        return Entorno::orderBy('orden')->get();
    }

    public function seleccionarEntorno(int $entornoId): void
    {
        $this->entorno_id = $entornoId;

        $entorno = Entorno::with('equipos')->find($entornoId);

        if (! $entorno) {
            return;
        }
        $this->equipos = [];
        foreach ($entorno->equipos as $equipo) {
            $this->equipos[$equipo->id] = [
                'seleccionado' => (bool) $equipo->pivot->seleccionado_defecto,
                'cantidad' => (int) $equipo->pivot->cantidad_defecto,
            ];
        }

        $this->prioridades = [];
        $this->resetResultado();
    }
    #[Computed]
    public function sugerenciasBusqueda(): Collection
    {
        if (strlen($this->busquedaEquipo) < 2) {
            return collect();
        }

        $idsExistentes = array_keys($this->equipos);
        return EquipoElectrico::where('nombre', 'like', '%' . $this->busquedaEquipo . '%')
            ->whereNotIn('id', $idsExistentes)
            ->limit(6)
            ->get();
    }

    public function agregarEquipo(int $equipoId): void
    {
        $this->equipos[$equipoId] = [
            'seleccionado' => true,
            'cantidad' => 1,
        ];

        $this->busquedaEquipo = '';
    }

    public function toggleEquipo(int $equipoId): void
    {
        if (! isset($this->equipos[$equipoId])) {
            return;
        }

        $this->equipos[$equipoId]['seleccionado'] = ! $this->equipos[$equipoId]['seleccionado'];
        if ($this->equipos[$equipoId]['seleccionado'] && $this->equipos[$equipoId]['cantidad'] < 1) {
            $this->equipos[$equipoId]['cantidad'] = 1;
        }
    }

    public function actualizarCantidad(int $equipoId, int $cantidad): void
    {
        $cantidad = max(0, $cantidad);

        $this->equipos[$equipoId]['cantidad'] = $cantidad;
        $this->equipos[$equipoId]['seleccionado'] = $cantidad > 0;
    }

    public function quitarEquipo(int $equipoId): void
    {
        unset($this->equipos[$equipoId], $this->prioridades[$equipoId]);
    }
    #[Computed]
    public function equiposActivos(): Collection
    {
        $ids = collect($this->equipos)
            ->filter(fn($e) => $e['seleccionado'] && $e['cantidad'] > 0)
            ->keys();

        return EquipoElectrico::whereIn('id', $ids)->get()->map(function (EquipoElectrico $equipo) {
            $equipo->cantidad_seleccionada = $this->equipos[$equipo->id]['cantidad'];

            return $equipo;
        });
    }

    #[Computed]
    public function catalogoCompleto(): Collection
    {
        return EquipoElectrico::whereIn('id', array_keys($this->equipos))->get()->keyBy('id');
    }

    public function marcarPrioridad(int $equipoId, string $tipo): void
    {
        if (($this->prioridades[$equipoId] ?? null) === $tipo) {
            unset($this->prioridades[$equipoId]);

            return;
        }

        $this->prioridades[$equipoId] = $tipo;
    }

    #[Computed]
    public function equiposCriticos(): Collection
    {
        return $this->equiposActivos()->filter(
            fn(EquipoElectrico $e) => ($this->prioridades[$e->id] ?? null) === 'critico'
        );
    }

    #[Computed]
    public function equiposIntermitentes(): Collection
    {
        return $this->equiposActivos()->filter(
            fn(EquipoElectrico $e) => ($this->prioridades[$e->id] ?? null) === 'intermitente'
        );
    }

    // =========================================================
    //  PASO 4 · CÁLCULO Y RECOMENDACIÓN
    // =========================================================

    public function calcularCapacidad(): void
    {
        $this->step = 4;
    }
    #[Computed]
    public function resultado(): array
    {
        $activos = $this->equiposActivos();

        $potenciaNominalW = $activos->sum(
            fn(EquipoElectrico $e) => $e->potencia_nominal_w * $e->cantidad_seleccionada
        );

        $mayorSurgeW = $activos
            ->filter(fn(EquipoElectrico $e) => $e->es_motor)
            ->map(fn(EquipoElectrico $e) => $e->surge_extra_watts)
            ->max() ?? 0;

        $potenciaRequeridaW = $potenciaNominalW + $mayorSurgeW;
        $margenW = $potenciaRequeridaW * $this->margenSeguridad;
        $potenciaTotalW = $potenciaRequeridaW + $margenW;

        $capacidadKw = round($potenciaTotalW / 1000, 2);
        $capacidadKva = round($capacidadKw / $this->factorPotencia, 2);

        return [
            'potencia_nominal_w' => $potenciaNominalW,
            'mayor_surge_w' => $mayorSurgeW,
            'margen_w' => round($margenW),
            'potencia_total_w' => round($potenciaTotalW),
            'capacidad_kw' => $capacidadKw,
            'capacidad_kva' => $capacidadKva,
        ];
    }

    #[Computed]
    public function generadoresRecomendados(): Collection
    {
        $combustiblesActivos = collect($this->filtroCombustible)
            ->filter()
            ->keys()
            ->toArray();

        return Generador::activos()
            ->combustibleEn($combustiblesActivos)
            ->conCapacidadMinima($this->resultado()['capacidad_kva'])
            ->orderBy('capacidad_kva')
            ->limit(4)
            ->get();
    }

    /** Generador elegido por el cliente para cotizar (se muestra en el modal). */
    #[Computed]
    public function generadorElegido(): ?Generador
    {
        return $this->generadorSeleccionado
            ? Generador::find($this->generadorSeleccionado)
            : null;
    }

    protected function resetResultado(): void
    {
        unset($this->resultado, $this->generadoresRecomendados);
    }


    public function siguientePaso(): void
    {
        if ($this->step === 1 && ! $this->entorno_id) {
            $this->addError('entorno', 'Selecciona un entorno para continuar.');

            return;
        }

        if ($this->step === 2 && $this->equiposActivos()->isEmpty()) {
            $this->addError('equipos', 'Selecciona al menos un equipo con cantidad mayor a cero.');

            return;
        }

        $this->step = min(4, $this->step + 1);
    }

    public function pasoAnterior(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function irAlPaso(int $paso): void
    {
        // Solo permite saltar hacia atrás libremente, o hacia adelante si ya hay entorno + equipos
        if ($paso <= $this->step || ($this->entorno_id && $this->equiposActivos()->isNotEmpty())) {
            $this->step = $paso;
        }
    }

    // =========================================================
    //  COTIZACIÓN / CONTACTO
    // =========================================================

    public function abrirModalCotizacion(?int $generadorId = null): void
    {
        // Si ya se envió una cotización, no se puede reabrir el formulario
        if ($this->cotizacionEnviada) {
            return;
        }

        $this->generadorSeleccionado = $generadorId;
        $this->resetValidation();
        $this->mostrarModalCotizacion = true;
    }

    public function cerrarModalCotizacion(): void
    {
        // Después de enviar, cerrar el modal reinicia todo y vuelve al inicio
        if ($this->cotizacionEnviada) {
            $this->nuevaCotizacion();

            return;
        }

        $this->mostrarModalCotizacion = false;
        $this->resetValidation();
    }

    public function enviarCotizacion(): void
    {
        // Protección 1: si ya se envió, no hacer nada (doble clic, reenvío)
        if ($this->cotizacionEnviada) {
            return;
        }

        $this->validate([
            'nombre_cliente' => 'required|string|max:120',
            'telefono' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'generadorSeleccionado' => 'nullable|exists:generadores,id',
        ]);

        // Protección 2: misma solicitud reciente (por ejemplo, tras recargar la página)
        $duplicada = Cotizacion::where('telefono', $this->telefono)
            ->where('entorno_id', $this->entorno_id)
            ->where('generador_id', $this->generadorSeleccionado)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($duplicada) {
            $this->addError('telefono', 'Ya recibimos una solicitud con este teléfono hace pocos minutos. Un asesor te contactará pronto.');

            return;
        }

        $resultado = $this->resultado();

        Cotizacion::create([
            'entorno_id' => $this->entorno_id,
            'generador_id' => $this->generadorSeleccionado,
            'equipos_json' => $this->equiposActivos()->map(fn(EquipoElectrico $e) => [
                'id' => $e->id,
                'nombre' => $e->nombre,
                'cantidad' => $e->cantidad_seleccionada,
                'prioridad' => $this->prioridades[$e->id] ?? null,
            ])->values()->toArray(),
            'potencia_total_w' => $resultado['potencia_total_w'],
            'capacidad_recomendada_kva' => $resultado['capacidad_kva'],
            'capacidad_recomendada_kw' => $resultado['capacidad_kw'],
            'nombre_cliente' => $this->nombre_cliente,
            'telefono' => $this->telefono,
            'email' => $this->email,
        ]);

        // El modal sigue abierto mostrando "¡Solicitud enviada!"
        $this->cotizacionEnviada = true;
    }

    /** Limpia todo y regresa al paso 1 para empezar una cotización nueva. */
    public function nuevaCotizacion(): void
    {
        $this->reset([
            'step',
            'entorno_id',
            'equipos',
            'busquedaEquipo',
            'prioridades',
            'filtroCombustible',
            'mostrarModalCotizacion',
            'nombre_cliente',
            'telefono',
            'email',
            'cotizacionEnviada',
            'generadorSeleccionado',
        ]);

        $this->resetValidation();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.cotizador')->layout('layouts.app');
    }
}
