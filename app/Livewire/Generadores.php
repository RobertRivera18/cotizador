<?php

namespace App\Livewire;

use App\Models\Equipo;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Generadores extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $equipoId = null;
    public string $tipo = 'generador';
    public string $nombre = '';
    public $capacidad_kva = '';
    public $capacidad_kw = '';
    public $marca = '';
    public $fases = '';
    public string $combustible = 'diesel';
    public $precio = null;
    public string $descripcion = '';
    public bool $activo = true;
    public $imagen = null;
    public ?string $imagenActual = null;

    public string $search = '';
    public bool $modalForm = false;

    protected function rules(): array
    {
        $rules = [
            'tipo'          => 'required|in:generador,bateria,inversor,panel solar',
            'nombre'        => 'required|string|max:255',
            'marca'         => 'nullable|string|max:255',
            'precio'        => 'nullable|numeric|min:0|max:99999999.99',
            'descripcion'   => 'nullable|string|max:5000',
            'activo'        => 'boolean',
            'imagen'        => 'nullable|image|max:2048',
        ];

        if ($this->tipo === 'generador') {
            $rules['capacidad_kva'] = 'nullable|numeric|min:0|max:999999.99';
            $rules['capacidad_kw']  = 'required|numeric|min:0|max:999999.99';
            $rules['fases']         = 'required|string|max:255';
            $rules['combustible']   = 'required|in:gasolina,diesel,gas';
        } else {
            // Para batería, inversor y panel solar se manejan como opcionales o específicos según tus necesidades
            $rules['capacidad_kva'] = 'nullable|numeric|min:0|max:999999.99';
            $rules['capacidad_kw']  = 'nullable|numeric|min:0|max:999999.99';
            $rules['fases']         = 'nullable|string|max:255';
            $rules['combustible']   = 'nullable|in:gasolina,diesel,gas';
        }

        return $rules;
    }

    private function autorizar(): void
    {
        abort_unless(auth()->user()?->can('Gestion de generadores'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->autorizar();
        $this->resetFormulario();
        $this->modalForm = true;
    }

    public function editar(int $id): void
    {
        $this->autorizar();

        $e = Equipo::findOrFail($id);

        $this->equipoId      = $e->id;
        $this->tipo          = $e->tipo ?? 'generador';
        $this->nombre        = $e->nombre;
        $this->capacidad_kva = $e->capacidad_kva;
        $this->capacidad_kw  = $e->capacidad_kw;
        $this->marca         = $e->marca ?? '';
        $this->fases         = $e->fases ?? '';
        $this->combustible   = $e->combustible ?? 'diesel';
        $this->precio        = $e->precio;
        $this->descripcion   = $e->descripcion ?? '';
        $this->activo        = $e->activo;
        $this->imagenActual  = $e->imagen;
        $this->imagen        = null;

        $this->resetValidation();
        $this->modalForm = true;
    }

    public function guardar(): void
    {
        $this->autorizar();

        $datos = $this->validate();

        $datos['precio'] = $datos['precio'] === '' ? null : $datos['precio'];
        $datos['descripcion'] = $this->limpiarHtml($datos['descripcion']);

        // Si el tipo no es generador, limpiamos campos que aplican solo a generadores
        if ($datos['tipo'] !== 'generador') {
            $datos['combustible'] = null;
            $datos['fases'] = null;
            $datos['capacidad_kva'] = $datos['capacidad_kva'] === '' ? null : $datos['capacidad_kva'];
            $datos['capacidad_kw'] = $datos['capacidad_kw'] === '' ? null : $datos['capacidad_kw'];
        }

        if ($this->imagen) {
            if ($this->imagenActual) {
                Storage::disk('public')->delete($this->imagenActual);
            }
            $datos['imagen'] = $this->imagen->store('equipos', 'public');
        } else {
            unset($datos['imagen']);
        }

        $esEdicion = (bool) $this->equipoId;

        Equipo::updateOrCreate(['id' => $this->equipoId], $datos);

        $this->cerrarModal();

        $this->dispatch(
            'swal',
            icon: 'success',
            title: $esEdicion ? '¡Actualizado!' : '¡Creado!',
            text: $esEdicion
                ? 'El equipo ha sido actualizado correctamente.'
                : 'El equipo ha sido creado correctamente.',
            timer: 3000,
            showConfirmButton: false
        );
    }

    public function eliminar(int $id): void
    {
        $this->autorizar();

        $e = Equipo::findOrFail($id);

        if ($e->imagen) {
            Storage::disk('public')->delete($e->imagen);
        }

        $e->delete();

        $this->dispatch(
            'swal',
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El equipo fue eliminado del sistema.',
            timer: 3000,
            showConfirmButton: false
        );
    }

    public function cerrarModal(): void
    {
        $this->modalForm = false;
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->reset([
            'equipoId',
            'tipo',
            'nombre',
            'capacidad_kva',
            'capacidad_kw',
            'marca',
            'fases',
            'combustible',
            'precio',
            'descripcion',
            'activo',
            'imagen',
            'imagenActual',
        ]);
        $this->tipo = 'generador';
        $this->combustible = 'diesel';
        $this->resetValidation();
    }

    private function limpiarHtml(?string $html): ?string
    {
        $html = trim((string) $html);

        if ($html === '' || trim(strip_tags($html)) === '') {
            return null;
        }

        $html = strip_tags($html, '<p><br><strong><em><u><ol><ul><li>');

        return preg_replace('/<(p|br|strong|em|u|ol|ul|li)\b[^>]*>/i', '<$1>', $html);
    }

    public function render()
    {
        $generadores = Equipo::query()
            ->when($this->search, fn($q) => $q->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('marca', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.generadores', compact('generadores'))->layout('layouts.admin');
    }
}
