<?php

use App\Http\Controllers\Admin\CotizacionController;
use App\Livewire\Generadores;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/generadores', Generadores::class)->name('admin.generadores');

Route::get('/cotizaciones/{id}/pdf', [CotizacionController::class, 'descargarPdf'])
    ->name('admin.cotizaciones.pdf');

Route::resource('cotizaciones', CotizacionController::class)
    ->parameters(['cotizaciones' => 'cotizacion'])
    ->names('admin.cotizaciones');
