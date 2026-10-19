<?php

namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Entorno;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CotizacionEdit extends Component
{
    public const TIPOS = [
        'generador' => 'Generador',
        'bateria'   => 'Batería',
    ];

    public Cotizacion $cotizacion;
    public $nombre_cliente = '';
    public $telefono = '';
    public $email = '';
    public $cedula_ruc = '';
    public $entorno_id = null;

    public array $items = [];
    public array $detallesEliminados = [];

    public function mount(Cotizacion $cotizacion): void
    {
        $this->cotizacion = $cotizacion;

        $this->nombre_cliente = $cotizacion->nombre_cliente;
        $this->telefono       = $cotizacion->telefono;
        $this->email          = $cotizacion->email;
        $this->cedula_ruc     = $cotizacion->cedula_ruc;
        $this->entorno_id     = $cotizacion->entorno_id;

        $detalles = $cotizacion->detalles;

        $this->items = $detalles->map(function ($d) {
            return [
                'id'              => $d->id,
                'equipo_id'       => $d->equipo_id ?? '',
                'descripcion'     => $d->descripcion,
                'cantidad'        => (int) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal'        => round($d->cantidad * $d->precio_unitario, 2),
            ];
        })->toArray();

        if (empty($this->items)) {
            $this->agregarFila();
        }
    }

    public function agregarFila(): void
    {
        $this->items[] = [
            'id'              => null,
            'equipo_id'       => '',
            'descripcion'     => '',
            'cantidad'        => 1,
            'precio_unitario' => 0.00,
            'subtotal'        => 0.00,
        ];
    }

    public function eliminarFila($index): void
    {
        if (!empty($this->items[$index]['id'])) {
            $this->detallesEliminados[] = $this->items[$index]['id'];
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);

        if (count($parts) !== 2) {
            return;
        }

        [$index, $campo] = $parts;

        if ($campo === 'equipo_id' && !empty($value)) {
            $equipo = Equipo::where('activo', true)->find($value);

            if ($equipo) {
                $this->items[$index]['descripcion']     = $equipo->nombre;
                $this->items[$index]['precio_unitario'] = $equipo->precio ?? 0.00;
            }
        }

        $cant   = (float) ($this->items[$index]['cantidad'] ?? 0);
        $precio = (float) ($this->items[$index]['precio_unitario'] ?? 0);
        $this->items[$index]['subtotal'] = round($cant * $precio, 2);
    }

    public function getTotalProperty(): float
    {
        return array_reduce($this->items, function ($carry, $item) {
            return $carry + ((float) ($item['cantidad'] ?? 0) * (float) ($item['precio_unitario'] ?? 0));
        }, 0.0);
    }

    public function actualizar()
    {
        $this->validate([
            'nombre_cliente'          => 'required|string|max:255',
            'telefono'                => 'nullable|string|max:50',
            'email'                   => 'nullable|email|max:255',
            'cedula_ruc'              => 'nullable|digits_between:10,13',
            'entorno_id'              => 'nullable|exists:entornos,id',
            'items'                   => 'required|array|min:1',
            'items.*.equipo_id'       => 'nullable|exists:equipos,id',
            'items.*.descripcion'     => 'required|string|max:255',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ], [
            'nombre_cliente.required'      => 'El nombre del cliente es obligatorio.',
            'items.required'               => 'Debe agregar al menos un ítem a la cotización.',
            'items.*.descripcion.required' => 'La descripción del ítem es obligatoria.',
            'items.*.cantidad.required'    => 'La cantidad debe ser al menos 1.',
            'items.*.equipo_id.exists'     => 'El equipo seleccionado ya no existe.',
        ]);

        DB::transaction(function () {
            $this->cotizacion->update([
                'nombre_cliente' => $this->nombre_cliente,
                'telefono'       => $this->telefono,
                'email'          => $this->email,
                'cedula_ruc'     => $this->cedula_ruc,
                'entorno_id'     => $this->entorno_id ?: null,
                'total'          => $this->total,
            ]);

            // Borrar los detalles que el usuario quitó (solo de esta cotización)
            if (!empty($this->detallesEliminados)) {
                CotizacionDetalle::where('cotizacion_id', $this->cotizacion->id)
                    ->whereIn('id', $this->detallesEliminados)
                    ->delete();
            }

            foreach ($this->items as $item) {
                $datos = [
                    'equipo_id'       => !empty($item['equipo_id']) ? $item['equipo_id'] : null,
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => round($item['cantidad'] * $item['precio_unitario'], 2),
                ];

                if (!empty($item['id'])) {
                    CotizacionDetalle::where('cotizacion_id', $this->cotizacion->id)
                        ->where('id', $item['id'])
                        ->update($datos);
                } else {
                    CotizacionDetalle::create($datos + ['cotizacion_id' => $this->cotizacion->id]);
                }
            }
        });

        session()->flash('message', 'Cotización actualizada con éxito.');

        return redirect()->route('admin.cotizaciones.index');
    }

    public function render()
    {
        $idsEnCotizacion = collect($this->items)->pluck('equipo_id')->filter()->all();
        return view('livewire.cotizacion-edit', [
            'tipos'          => self::TIPOS,
            'equiposPorTipo' => Equipo::where(function ($q) use ($idsEnCotizacion) {
                    $q->where('activo', true)->orWhereIn('id', $idsEnCotizacion);
                })
                ->orderBy('tipo')
                ->orderBy('nombre')
                ->get(['id', 'tipo', 'nombre', 'marca', 'precio', 'capacidad_kw', 'capacidad_kva', 'fases'])
                ->groupBy('tipo'),
            'entornos'       => Entorno::all(),
        ]);
    }
}