<?php

use App\Livewire\Cotizador;
use Illuminate\Support\Facades\Route;

Route::get('/', Cotizador::class);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
