<?php

namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Generador;
use App\Models\Entorno;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CotizacionEdit extends Component
{
    public Cotizacion $cotizacion;

    public $nombre_cliente = '';
    public $telefono = '';
    public $email = '';
    public $cedula_ruc = '';
    public $entorno_id = null;

    public $items = [];
    public $detallesEliminados = [];

    public function mount(Cotizacion $cotizacion)
    {
        $this->cotizacion = $cotizacion;

        $this->nombre_cliente = $cotizacion->nombre_cliente;
        $this->telefono       = $cotizacion->telefono;
        $this->email          = $cotizacion->email;
        $this->cedula_ruc     = $cotizacion->cedula_ruc;
        $this->entorno_id     = $cotizacion->entorno_id;

        $this->items = $cotizacion->detalles->map(function ($d) {
            return [
                'id'              => $d->id,
                'generador_id'    => $d->generador_id ?? '',
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

    public function agregarFila()
    {
        $this->items[] = [
            'id'              => null,
            'generador_id'    => '',
            'descripcion'     => '',
            'cantidad'        => 1,
            'precio_unitario' => 0.00,
            'subtotal'        => 0.00,
        ];
    }

    public function eliminarFila($index)
    {
        if (!empty($this->items[$index]['id'])) {
            $this->detallesEliminados[] = $this->items[$index]['id'];
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $campo = $parts[1];

            if ($campo === 'generador_id' && !empty($value)) {
                $generador = Generador::find($value);
                if ($generador) {
                    $this->items[$index]['descripcion'] = $generador->nombre ?? $generador->modelo;
                    $this->items[$index]['precio_unitario'] = $generador->precio ?? 0.00;
                }
            }

            $cant   = (float) ($this->items[$index]['cantidad'] ?? 0);
            $precio = (float) ($this->items[$index]['precio_unitario'] ?? 0);
            $this->items[$index]['subtotal'] = round($cant * $precio, 2);
        }
    }

    public function getTotalProperty()
    {
        return array_reduce($this->items, function ($carry, $item) {
            return $carry + ((float) ($item['cantidad'] ?? 0) * (float) ($item['precio_unitario'] ?? 0));
        }, 0);
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
            'items.*.descripcion'     => 'required|string|max:255',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.generador_id'    => 'nullable',
        ], [
            'nombre_cliente.required'      => 'El nombre del cliente es obligatorio.',
            'items.required'               => 'Debe agregar al menos un ítem a la cotización.',
            'items.*.descripcion.required' => 'La descripción del ítem es obligatoria.',
            'items.*.cantidad.required'    => 'La cantidad debe ser al menos 1.',
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
                    'generador_id'    => !empty($item['generador_id']) ? $item['generador_id'] : null,
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $item['cantidad'] * $item['precio_unitario'],
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
        return view('livewire.cotizacion-edit', [
            'generadores' => Generador::where('activo', true)->get(),
            'entornos'    => Entorno::all(),
        ]);
    }
}
