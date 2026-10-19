<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Equipo extends Model
{
    protected $table = "equipos";
    
    protected $fillable = [
        'tipo',
        'nombre',
        'capacidad_kva',
        'capacidad_kw',
        'combustible',
        'marca',
        'fases',
        'precio',
        'imagen',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'capacidad_kva' => 'decimal:2',
        'capacidad_kw' => 'decimal:2',
        'precio' => 'decimal:2',
    ];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeGeneradores(Builder $query): Builder
    {
        return $query->where('tipo', 'generador');
    }

    public function scopeBaterias(Builder $query): Builder
    {
        return $query->where('tipo', 'bateria');
    }
    
    public function scopeCombustibleEn(Builder $query, array $combustibles): Builder
    {
        return count($combustibles) ? $query->where('tipo', 'generador')->whereIn('combustible', $combustibles) : $query;
    }

    public function scopeConCapacidadMinima(Builder $query, float $kva): Builder
    {
        return $query->where('tipo', 'generador')->where('capacidad_kva', '>=', $kva);
    }
}