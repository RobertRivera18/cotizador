<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'Acceso al Dashboard',

            'Gestion de generadores',

            'Ver cotizaciones',
            'Crear cotizaciones',
            'Editar cotizaciones',
            'Eliminar cotizaciones',
            'Descargar PDF cotizaciones',

            'Gestion de roles',
            'Gestion de permisos',
            'Gestion de Usuarios',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());
        $asesor = Role::firstOrCreate(['name' => 'Asesor', 'guard_name' => 'web']);
        $asesor->syncPermissions([
            'Acceso al Dashboard',
            'Ver cotizaciones',
            'Crear cotizaciones',
            'Editar cotizaciones',
            'Descargar PDF cotizaciones',
        ]);
        User::where('email', 'rxrc1819@gmail.com')->first()?->assignRole('Administrador');
    }
}
