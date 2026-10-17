<?php
namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\Entorno;
use App\Models\EquipoElectrico;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CotizacionesAll extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroEntorno = '';
    public ?int $cotizacionId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEntorno(): void
    {
        $this->resetPage();
    }

    public function ver(int $id): void
    {
        $this->cotizacionId = $id;
    }

    public function cerrar(): void
    {
        $this->cotizacionId = null;
    }

    public function eliminar(int $id): void
    {
        Cotizacion::findOrFail($id)->delete();

        if ($this->cotizacionId === $id) {
            $this->cotizacionId = null;
        }

        $this->dispatch(
            'swal',
            icon: 'success',
            title: '¡Eliminada!',
            text: 'La cotización fue eliminada del sistema.',
        );
    }

    #[Computed]
    public function cotizacion(): ?Cotizacion
    {
        return $this->cotizacionId
            ? Cotizacion::with(['entorno', 'generador', 'detalles.generador'])->find($this->cotizacionId)
            : null;
    }

    #[Computed]
    public function equiposDetalle(): array
    {
        $c = $this->cotizacion;

        if (! $c || empty($c->equipos_json)) {
            return [];
        }

        $items = collect($c->equipos_json ?? []);

        $catalogo = EquipoElectrico::whereIn('id', $items->pluck('id')->filter())
            ->get()
            ->keyBy('id');

        return $items->map(fn($i) => [
            'nombre'     => $i['nombre'] ?? '—',
            'cantidad'   => (int) ($i['cantidad'] ?? 0),
            'prioridad'  => $i['prioridad'] ?? null,
            'potencia_w' => $catalogo->get($i['id'] ?? 0)?->potencia_nominal_w,
        ])->all();
    }

    #[Computed]
    public function resumenTexto(): string
    {
        $c = $this->cotizacion;

        if (! $c) {
            return '';
        }

        $lineas = [
            "COTIZACIÓN #{$c->id} · {$c->created_at->format('d/m/Y H:i')}",
            '',
            "Cliente: {$c->nombre_cliente}",
            "Teléfono: {$c->telefono}",
            'Email: ' . ($c->email ?: '—'),
            'Origen: ' . strtoupper($c->tipo_origen ?? 'web'),
            '',
        ];

        // Si es una cotización manual con tabla de detalles
        if ($c->detalles && $c->detalles->count() > 0) {
            $lineas[] = 'Ítems cotizados:';
            foreach ($c->detalles as $d) {
                $lineas[] = "- {$d->cantidad} x {$d->descripcion} ($" . number_format($d->precio_unitario, 2) . ") = $" . number_format($d->subtotal, 2);
            }
            $subtotal = $c->detalles->sum('subtotal');
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;
            $lineas[] = '';
            $lineas[] = "Subtotal: $" . number_format($subtotal, 2);
            $lineas[] = "IVA (15%): $" . number_format($iva, 2);
            $lineas[] = "TOTAL: $" . number_format($total, 2);
        } else {
            // Si es una cotización automática de la web
            $lineas[] = 'Entorno: ' . ($c->entorno?->nombre ?? '—');
            $lineas[] = "Potencia total: {$c->potencia_total_w} W";
            $lineas[] = "Capacidad recomendada: {$c->capacidad_recomendada_kva} kVA / {$c->capacidad_recomendada_kw} kW";
            $lineas[] = 'Modelo elegido: ' . ($c->generador?->nombre ?? 'Sin modelo (consulta general)');
            $lineas[] = '';
            $lineas[] = 'Equipos a respaldar:';

            foreach ($this->equiposDetalle as $e) {
                $linea = "- {$e['cantidad']} x {$e['nombre']}";
                if ($e['potencia_w']) {
                    $linea .= " ({$e['potencia_w']} W)";
                }
                if ($e['prioridad']) {
                    $linea .= ' [' . ucfirst($e['prioridad']) . ']';
                }
                $lineas[] = $linea;
            }
        }

        return implode("\n", $lineas);
    }

    public function waLink(?string $telefono, ?string $nombre = null): string
    {
        $numero = preg_replace('/\D+/', '', (string) $telefono);

        if (str_starts_with($numero, '0')) {
            $numero = '593' . substr($numero, 1);
        }

        $mensaje = 'Hola' . ($nombre ? " {$nombre}" : '')
            . ', le escribimos por su solicitud de cotización de generador. '
            . '¿Podemos coordinar los detalles para enviarle su proforma?';

        return "https://wa.me/{$numero}?text=" . rawurlencode($mensaje);
    }

    public function render()
    {
        $cotizaciones = Cotizacion::query()
            ->with(['entorno', 'generador', 'detalles'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('nombre_cliente', 'like', "%{$this->search}%")
                        ->orWhere('telefono', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filtroEntorno, fn($q) => $q->where('entorno_id', $this->filtroEntorno))
            ->latest()
            ->paginate(10);

        return view('livewire.cotizaciones-all', [
            'cotizaciones' => $cotizaciones,
            'entornos'     => Entorno::orderBy('orden')->get(),
        ]);
    }
}