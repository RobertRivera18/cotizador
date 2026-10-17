<?php

use App\Livewire\Generadores;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/generadores', Generadores::class)->name('admin.generadores');
