<?php

namespace App\Livewire;

use App\Models\Generador;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Generadores extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $generadorId = null;
    public string $nombre = '';
    public $capacidad_kva = '';
    public $capacidad_kw = '';
    public string $combustible = 'diesel';
    public $precio = null;
    public string $descripcion = '';     // HTML generado por el editor Quill (se limpia al guardar)
    public bool $activo = true;
    public $imagen = null;               // archivo nuevo subido
    public ?string $imagenActual = null; // ruta guardada

    // UI
    public string $search = '';
    public bool $modalForm = false;

    protected function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:255',
            'capacidad_kva' => 'required|numeric|min:0|max:999999.99',
            'capacidad_kw'  => 'required|numeric|min:0|max:999999.99',
            'combustible'   => 'required|in:gasolina,diesel,gas',
            'precio'        => 'nullable|numeric|min:0|max:99999999.99',
            'descripcion'   => 'nullable|string|max:5000',
            'activo'        => 'boolean',
            'imagen'        => 'nullable|image|max:2048',
        ];
    }

    /**
     * El middleware de la ruta solo protege la carga de la página.
     * Los métodos de Livewire se pueden invocar por separado, así que se comprueba aquí también.
     */
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

        $g = Generador::findOrFail($id);

        $this->generadorId   = $g->id;
        $this->nombre        = $g->nombre;
        $this->capacidad_kva = $g->capacidad_kva;
        $this->capacidad_kw  = $g->capacidad_kw;
        $this->combustible   = $g->combustible;
        $this->precio        = $g->precio;
        $this->descripcion   = $g->descripcion ?? '';
        $this->activo        = $g->activo;
        $this->imagenActual  = $g->imagen;
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

        if ($this->imagen) {
            if ($this->imagenActual) {
                Storage::disk('public')->delete($this->imagenActual);
            }
            $datos['imagen'] = $this->imagen->store('generadores', 'public');
        } else {
            unset($datos['imagen']);
        }

        $esEdicion = (bool) $this->generadorId;

        Generador::updateOrCreate(['id' => $this->generadorId], $datos);

        $this->cerrarModal();

        $this->dispatch('swal',
            icon: 'success',
            title: $esEdicion ? '¡Actualizado!' : '¡Creado!',
            text: $esEdicion
                ? 'El generador ha sido actualizado correctamente.'
                : 'El generador ha sido creado correctamente.',
            timer: 3000,
            showConfirmButton: false
        );
    }

    public function eliminar(int $id): void
    {
        $this->autorizar();

        $g = Generador::findOrFail($id);

        if ($g->imagen) {
            Storage::disk('public')->delete($g->imagen);
        }

        $g->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El generador fue eliminado del sistema.',
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
            'generadorId', 'nombre', 'capacidad_kva', 'capacidad_kw',
            'combustible', 'precio', 'descripcion', 'activo',
            'imagen', 'imagenActual',
        ]);
        $this->resetValidation();
    }

    /**
     * Deja solo el formato básico que genera el editor (negrita, cursiva, subrayado, listas)
     * y elimina cualquier atributo (onclick, style, etc.) para que sea seguro mostrarlo con {!! !!}.
     */
    private function limpiarHtml(?string $html): ?string
    {
        $html = trim((string) $html);

        // Vacío, o solo etiquetas sin texto
        if ($html === '' || trim(strip_tags($html)) === '') {
            return null;
        }

        $html = strip_tags($html, '<p><br><strong><em><u><ol><ul><li>');

        return preg_replace('/<(p|br|strong|em|u|ol|ul|li)\b[^>]*>/i', '<$1>', $html);
    }

    public function render()
    {
        $generadores = Generador::query()
            ->when($this->search, fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(5);

        return view('livewire.generadores', compact('generadores'))->layout('layouts.admin');
    }
}
