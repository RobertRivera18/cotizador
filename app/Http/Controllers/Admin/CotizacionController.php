<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('admin.cotizaciones.index');
    }

    public function create()
    {
        return view('admin.cotizaciones.create');
    }

    public function edit(Cotizacion $cotizacion)
{
    return view('admin.cotizaciones.edit', compact('cotizacion'));
}
    public function descargarPdf($id)
    {
        $cotizacion = Cotizacion::with(['detalles', 'entorno'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.pdf.cotizacion', compact('cotizacion'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("Cotizacion_DISEIN_{$cotizacion->id}.pdf");
    }
}
