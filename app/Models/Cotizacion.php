<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = "cotizaciones";

    protected $fillable = [
        'entorno_id',
        'equipo_id',
        'equipos_json',
        'potencia_total_w',
        'capacidad_recomendada_kva',
        'capacidad_recomendada_kw',
        'nombre_cliente',
        'telefono',
        'email',
        'cedula_ruc',
        'estado',
        'tipo_origen',
        'total',
    ];

    protected $casts = [
        'equipos_json' => 'array',
        'capacidad_recomendada_kva' => 'decimal:2',
        'capacidad_recomendada_kw' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function entorno(): BelongsTo
    {
        return $this->belongsTo(Entorno::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
}
