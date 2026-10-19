<?php

namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Entorno;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CotizacionCreate extends Component
{
    public const TIPOS = [
        'generador' => 'Generador',
        'bateria'   => 'Batería',
    ];

    public $nombre_cliente = '';
    public $telefono = '';
    public $email = '';
    public $cedula_ruc = '';
    public $entorno_id = null;
    public array $items = [];

    public function mount(): void
    {
        $this->agregarFila();
    }

    public function agregarFila(): void
    {
        $this->items[] = [
            'equipo_id'       => '',
            'descripcion'     => '',
            'cantidad'        => 1,
            'precio_unitario' => 0.00,
            'subtotal'        => 0.00,
        ];
    }

    public function eliminarFila($index): void
    {
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

    public function guardar()
    {
        $this->validate([
            'nombre_cliente'          => 'required|string|max:255',
            'telefono'                => 'nullable|string|max:50',
            'email'                   => 'nullable|email|max:255',
            'cedula_ruc'              => 'nullable|digits_between:10,13',
            'entorno_id'              => 'nullable|exists:entornos,id',
            'items'                   => 'required|array|min:1',
            'items.*.equipo_id'       => ['nullable', Rule::exists('equipos', 'id')->where('activo', true)],
            'items.*.descripcion'     => 'required|string|max:255',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ], [
            'nombre_cliente.required'      => 'El nombre del cliente es obligatorio.',
            'items.required'               => 'Debe agregar al menos un ítem a la cotización.',
            'items.*.descripcion.required' => 'La descripción del ítem es obligatoria.',
            'items.*.cantidad.required'    => 'La cantidad debe ser al menos 1.',
            'items.*.equipo_id.exists'     => 'El equipo seleccionado ya no está disponible.',
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
                    'equipo_id'       => !empty($item['equipo_id']) ? $item['equipo_id'] : null,
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => round($item['cantidad'] * $item['precio_unitario'], 2),
                ]);
            }
        });

        session()->flash('message', 'Cotización manual creada con éxito.');

        return redirect()->route('admin.cotizaciones.index');
    }

    public function render()
    {
        return view('livewire.cotizacion-create', [
            'tipos'          => self::TIPOS,
            'equiposPorTipo' => Equipo::where('activo', true)
                ->orderBy('tipo')
                ->orderBy('nombre')
                ->get(['id', 'tipo', 'nombre', 'marca', 'precio', 'capacidad_kw', 'capacidad_kva', 'fases'])
                ->groupBy('tipo'),
            'entornos'       => Entorno::all(),
        ]);
    }
}
