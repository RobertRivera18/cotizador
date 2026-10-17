<?php

use App\Livewire\Generadores;
use App\Http\Controllers\Admin\CotizacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/generadores', Generadores::class)->name('admin.generadores');
Route::resource('/cotizaciones', CotizacionController::class)->names('admin.cotizaciones');
