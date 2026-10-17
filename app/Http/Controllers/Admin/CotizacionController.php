<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('admin.cotizaciones.index');
    }
}
