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
    // Datos del Cliente
    public $nombre_cliente = '';
    public $telefono = '';
    public $email = '';
    public $entorno_id = null; // Opcional por si quieres clasificar el entorno

    // Catálogos para selectores
    public $generadores = [];
    public $entornos = [];

    // Detalle dinámico
    public $items = [];

    public function mount()
    {
        $this->generadores = Generador::where('activo', true)->get();
        $this->entornos = Entorno::all();

        // Inicializamos con una fila vacía por defecto
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
        $this->items = array_values($this->items); // Reindexar el array
    }

    // Al seleccionar un generador, se llena automáticamente la descripción y el precio
    public function updatedItems($value, $key)
    {
        // Se activa cuando cambia algún campo de items (ej. "0.generador_id")
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

            // Recalcular subtotal de la fila
            $cant = (float) ($this->items[$index]['cantidad'] ?? 0);
            $precio = (float) ($this->items[$index]['precio_unitario'] ?? 0);
            $this->items[$index]['subtotal'] = round($cant * $precio, 2);
        }
    }

    // Propiedad computada para obtener el total general
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
            // Guardar cabecera (los campos calculados como equipos_json, potencias, etc. quedan NULL)
            $cotizacion = Cotizacion::create([
                'nombre_cliente' => $this->nombre_cliente,
                'telefono'       => $this->telefono,
                'email'          => $this->email,
                'entorno_id'     => $this->entorno_id ?: null,
                'tipo_origen'    => 'manual',
                'estado'         => 'pendiente',
                'total'          => $this->total,
            ]);

            // Guardar detalle
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