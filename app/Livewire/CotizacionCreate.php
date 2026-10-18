<?php

namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Generador;
use App\Models\Entorno;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CotizacionCreate extends Component
{
    public $nombre_cliente = '';
    public $telefono = '';
    public $email = '';
    public $cedula_ruc = '';
    public $entorno_id = null;
    public $generadores = [];
    public $entornos = [];

    public $items = [];

    public function mount()
    {
        $this->generadores = Generador::where('activo', true)->get();
        $this->entornos = Entorno::all();
        $this->agregarFila();
    }

    public function agregarFila()
    {
        $this->items[] = [
            'generador_id'    => '',
            'descripcion'     => '',
            'cantidad'        => 1,
            'precio_unitario' => 0.00,
            'subtotal'        => 0.00,
        ];
    }

    public function eliminarFila($index)
    {
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

            $cant = (float) ($this->items[$index]['cantidad'] ?? 0);
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

    public function guardar()
    {
        $this->validate([
            'nombre_cliente'          => 'required|string|max:255',
            'telefono'                => 'nullable|string|max:50',
            'email'                   => 'nullable|email|max:255',
            'cedula_ruc'                => 'nullable|digits_between:10,13',
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
            $cotizacion = Cotizacion::create([
                'nombre_cliente' => $this->nombre_cliente,
                'telefono'       => $this->telefono,
                'email'          => $this->email,
                'cedula_ruc'     => $this->cedula_ruc,
                'entorno_id'     => $this->entorno_id ?: null,
                'tipo_origen'    => 'manual',
                'estado'         => 'pendiente',
                'total'          => $this->total,
            ]);

            foreach ($this->items as $item) {
                CotizacionDetalle::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'generador_id'    => !empty($item['generador_id']) ? $item['generador_id'] : null,
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $item['cantidad'] * $item['precio_unitario'],
                ]);
            }
        });

        session()->flash('message', 'Cotización manual creada con éxito.');
        return redirect()->route('admin.cotizaciones.index');
    }

    public function render()
    {
        return view('livewire.cotizacion-create');
    }
}
