<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /** Rol con acceso total: no se puede renombrar ni eliminar. */
    private const ROL_PROTEGIDO = 'Administrador';

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $data['name']]);
        // Los checkboxes llegan como texto ("1"); Spatie solo interpreta enteros como IDs,
        // así que se cargan los modelos para evitar que busque permisos por nombre.
        $role->syncPermissions(
            Permission::whereIn('id', $data['permissions'] ?? [])->get()
        );

        $this->swal('Rol creado con éxito');

        return redirect()->route('admin.roles.edit', $role);
    }



    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($role->name === self::ROL_PROTEGIDO) {
            // El rol administrador conserva su nombre y todos sus permisos
            abort_if($data['name'] !== self::ROL_PROTEGIDO, 403, 'Este rol no se puede renombrar.');
        }

        $role->update(['name' => $data['name']]);
        // Los checkboxes llegan como texto ("1"); Spatie solo interpreta enteros como IDs,
        // así que se cargan los modelos para evitar que busque permisos por nombre.
        $role->syncPermissions(
            Permission::whereIn('id', $data['permissions'] ?? [])->get()
        );

        $this->swal('Rol actualizado con éxito');

        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role)
    {
        abort_if($role->name === self::ROL_PROTEGIDO, 403, 'Este rol no se puede eliminar.');

        $role->delete();

        $this->swal('Rol eliminado con éxito');

        return redirect()->route('admin.roles.index');
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
