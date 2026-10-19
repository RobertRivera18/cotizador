<?php

use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Generadores;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->middleware('can:Acceso al Dashboard')
    ->name('admin.dashboard');

Route::resource('/users', UserController::class)
    ->middleware('can:Gestion de Usuarios')
    ->names('admin.users');

Route::resource('/roles', RoleController::class)
    ->middleware('can:Gestion de roles')
    ->names('admin.roles');

Route::resource('/permissions', PermissionController::class)
    ->middleware('can:Gestion de permisos')
    ->names('admin.permissions');

Route::get('/generadores', Generadores::class)
    ->middleware('can:Gestion de generadores')
    ->name('admin.generadores');


Route::get('/cotizaciones', [CotizacionController::class, 'index'])
    ->middleware('can:Ver cotizaciones')
    ->name('admin.cotizaciones.index');

Route::get('/cotizaciones/create', [CotizacionController::class, 'create'])
    ->middleware('can:Crear cotizaciones')
    ->name('admin.cotizaciones.create');

Route::get('/cotizaciones/{cotizacion}/edit', [CotizacionController::class, 'edit'])
    ->middleware('can:Editar cotizaciones')
    ->name('admin.cotizaciones.edit');

Route::get('/cotizaciones/{id}/pdf', [CotizacionController::class, 'descargarPdf'])
    ->middleware('can:Descargar PDF cotizaciones')
    ->name('admin.cotizaciones.pdf');
