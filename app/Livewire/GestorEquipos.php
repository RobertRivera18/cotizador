<?php

namespace App\Livewire;

use App\Models\Entorno;
use App\Models\EquipoElectrico;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Crea / edita equipos eléctricos y los asigna a los entornos
 * (pivot entorno_equipo: cantidad_defecto, seleccionado_defecto, orden).
 *
 * Ubicación sugerida: app/Livewire/GestorEquipos.php
 * Vista: resources/views/livewire/gestor-equipos.blade.php
 * Uso:   <livewire:gestor-equipos />
 */
class GestorEquipos extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showModal = false;
    public ?int $equipoId = null;

    // Datos del equipo
    public string $nombre = '';
    public ?string $icono = '';
    public $potencia_nominal_w = null;
    public $potencia_arranque_w = null;
    public bool $es_motor = false;

    /**
     * [entorno_id => ['activo' => bool, 'cantidad' => int, 'seleccionado' => bool, 'orden' => int]]
     */
    public array $asignaciones = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('equiposelectricos', 'nombre')->ignore($this->equipoId),
            ],
            'icono' => ['nullable', 'string', 'max:10'],
            'potencia_nominal_w' => ['required', 'integer', 'min:1', 'max:1000000'],
            'es_motor' => ['boolean'],
            'potencia_arranque_w' => [
                Rule::requiredIf($this->es_motor),
                'nullable', 'integer', 'min:1', 'max:3000000',
                'gte:potencia_nominal_w',
            ],
            'asignaciones.*.activo' => ['boolean'],
            'asignaciones.*.cantidad' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'asignaciones.*.seleccionado' => ['boolean'],
            'asignaciones.*.orden' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'nombre' => 'nombre',
            'potencia_nominal_w' => 'potencia nominal',
            'potencia_arranque_w' => 'potencia de arranque',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un equipo con ese nombre.',
            'potencia_arranque_w.required' => 'Indica la potencia de arranque para equipos con motor.',
            'potencia_arranque_w.gte' => 'La potencia de arranque debe ser mayor o igual a la nominal.',
        ];
    }

    /** Estructura vacía de asignaciones: todos los entornos, inactivos. */
    private function asignacionesVacias(): array
    {
        $ultimoOrden = DB::table('entorno_equipo')
            ->select('entorno_id', DB::raw('MAX(orden) as max_orden'))
            ->groupBy('entorno_id')
            ->pluck('max_orden', 'entorno_id');

        $out = [];
        foreach (Entorno::orderBy('orden')->get(['id']) as $entorno) {
            $out[$entorno->id] = [
                'activo' => false,
                'cantidad' => 0,
                'seleccionado' => false,
                'orden' => ((int) ($ultimoOrden[$entorno->id] ?? 0)) + 1,
            ];
        }

        return $out;
    }

    public function crear(): void
    {
        $this->resetValidation();
        $this->reset(['equipoId', 'nombre', 'icono', 'potencia_nominal_w', 'potencia_arranque_w', 'es_motor']);
        $this->asignaciones = $this->asignacionesVacias();
        $this->showModal = true;
    }

    public function editar(int $id): void
    {
        $this->resetValidation();
        $equipo = EquipoElectrico::with('entornos')->findOrFail($id);

        $this->equipoId = $equipo->id;
        $this->nombre = $equipo->nombre;
        $this->icono = $equipo->icono;
        $this->potencia_nominal_w = $equipo->potencia_nominal_w;
        $this->potencia_arranque_w = $equipo->potencia_arranque_w;
        $this->es_motor = (bool) $equipo->es_motor;

        $asignaciones = $this->asignacionesVacias();
        foreach ($equipo->entornos as $entorno) {
            $asignaciones[$entorno->id] = [
                'activo' => true,
                'cantidad' => (int) $entorno->pivot->cantidad_defecto,
                'seleccionado' => (bool) $entorno->pivot->seleccionado_defecto,
                'orden' => (int) $entorno->pivot->orden,
            ];
        }
        $this->asignaciones = $asignaciones;

        $this->showModal = true;
    }

    public function updatedEsMotor(bool $value): void
    {
        if (! $value) {
            $this->potencia_arranque_w = null;
            $this->resetValidation('potencia_arranque_w');
        } elseif (! $this->potencia_arranque_w && $this->potencia_nominal_w) {
            // Sugerencia: arranque ≈ 3x la nominal
            $this->potencia_arranque_w = (int) $this->potencia_nominal_w * 3;
        }
    }

    public function guardar(): void
    {
        $this->validate();

        DB::transaction(function () {
            $equipo = EquipoElectrico::updateOrCreate(
                ['id' => $this->equipoId],
                [
                    'nombre' => trim($this->nombre),
                    'icono' => $this->icono ?: null,
                    'potencia_nominal_w' => (int) $this->potencia_nominal_w,
                    'potencia_arranque_w' => $this->es_motor ? (int) $this->potencia_arranque_w : null,
                    'es_motor' => $this->es_motor,
                ]
            );

            // sync solo de los entornos marcados; quita el equipo de los desmarcados
            $sync = [];
            foreach ($this->asignaciones as $entornoId => $a) {
                if (! empty($a['activo'])) {
                    $sync[$entornoId] = [
                        'cantidad_defecto' => (int) ($a['cantidad'] ?? 0),
                        'seleccionado_defecto' => (bool) ($a['seleccionado'] ?? false),
                        'orden' => (int) ($a['orden'] ?? 0),
                    ];
                }
            }
            $equipo->entornos()->sync($sync);
        });

        $this->showModal = false;
        session()->flash('ok', $this->equipoId ? 'Equipo actualizado.' : 'Equipo creado.');
        $this->reset('equipoId');
    }

    public function eliminar(int $id): void
    {
        DB::transaction(function () use ($id) {
            $equipo = EquipoElectrico::findOrFail($id);
            $equipo->entornos()->detach();
            $equipo->delete();
        });

        session()->flash('ok', 'Equipo eliminado.');
    }

    public function cerrar(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $equipos = EquipoElectrico::with('entornos:id,nombre')
            ->when($this->search, fn ($q) => $q->where('nombre', 'like', '%' . $this->search . '%'))
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.gestor-equipos', [
            'equipos' => $equipos,
            'entornos' => Entorno::orderBy('orden')->get(['id', 'nombre', 'slug']),
        ])->layout('layouts.admin');
    }
}