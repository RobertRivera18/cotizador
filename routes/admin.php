<?php

use Illuminate\Support\Facades\Route;
Route::get('/test', function () {
    return '¡Hola! Esta es una ruta de prueba dentro de admin.php en Laravel 12.';
})->name('admin.test');
Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');