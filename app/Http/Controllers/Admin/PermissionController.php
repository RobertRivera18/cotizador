<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        // Solo 'name': con $request->all() se podría inyectar guard_name u otros campos
        Permission::create(['name' => $data['name']]);

        $this->swal('Permiso creado con éxito');

        return redirect()->route('admin.permissions.index');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update(['name' => $data['name']]);

        $this->swal('Permiso actualizado con éxito', 3500);

        return redirect()->route('admin.permissions.index');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        $this->swal('Permiso eliminado con éxito', 3500);

        return redirect()->route('admin.permissions.index');
    }

    private function swal(string $text, int $timer = 3000): void
    {
        session()->flash('swal', [
            'icon'              => 'success',
            'title'             => '¡Bien hecho!',
            'text'              => $text,
            'position'          => 'top-end',
            'toast'             => true,
            'timer'             => $timer,
            'showConfirmButton' => false,
        ]);
    }
}
