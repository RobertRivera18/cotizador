<?php

namespace Database\Seeders;

use App\Models\Entorno;
use App\Models\EquipoElectrico;
use App\Models\Generador;
use Illuminate\Database\Seeder;

class GeneradorConfiguradorSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- ENTORNOS ----------
        $entornos = [
            ['nombre' => 'Residencial', 'slug' => 'residencial', 'icono' => 'home', 'orden' => 1],
            ['nombre' => 'Comercial', 'slug' => 'comercial', 'icono' => 'building', 'orden' => 2],
            ['nombre' => 'Industrial', 'slug' => 'industrial', 'icono' => 'factory', 'orden' => 3],
            ['nombre' => 'Eventos', 'slug' => 'eventos', 'icono' => 'sparkles', 'orden' => 4],
            ['nombre' => 'Campamento', 'slug' => 'campamento', 'icono' => 'tent', 'orden' => 5],
        ];

        foreach ($entornos as $data) {
            Entorno::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // ---------- CATÁLOGO DE EQUIPOS ----------
        // potencia_arranque_w solo aplica a equipos con motor (es_motor = true)
        $equipos = [
            ['nombre' => 'Refrigeradora', 'icono' => '🧊', 'potencia_nominal_w' => 150, 'potencia_arranque_w' => 450, 'es_motor' => true],

            // Aires acondicionados (arranque ≈ 3x la potencia nominal)
            ['nombre' => 'Aire Acond. 12k BTU', 'icono' => '❄️', 'potencia_nominal_w' => 1200, 'potencia_arranque_w' => 3600, 'es_motor' => true],
            ['nombre' => 'Aire Acond. 18k BTU', 'icono' => '❄️', 'potencia_nominal_w' => 1800, 'potencia_arranque_w' => 5400, 'es_motor' => true],
            ['nombre' => 'Aire Acond. 24k BTU', 'icono' => '❄️', 'potencia_nominal_w' => 2400, 'potencia_arranque_w' => 7200, 'es_motor' => true],
            ['nombre' => 'Aire Acond. 36k BTU', 'icono' => '❄️', 'potencia_nominal_w' => 3600, 'potencia_arranque_w' => 10800, 'es_motor' => true],

            ['nombre' => 'Luces LED', 'icono' => '💡', 'potencia_nominal_w' => 10, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'TV', 'icono' => '📺', 'potencia_nominal_w' => 120, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Lavadora', 'icono' => '🧺', 'potencia_nominal_w' => 500, 'potencia_arranque_w' => 1500, 'es_motor' => true],

            // Bombas de agua
            ['nombre' => 'Bomba de Agua', 'icono' => '🚰', 'potencia_nominal_w' => 750, 'potencia_arranque_w' => 2200, 'es_motor' => true],
            ['nombre' => 'Bomba de Agua Residencial', 'icono' => '💧', 'potencia_nominal_w' => 400, 'potencia_arranque_w' => 1200, 'es_motor' => true],

            ['nombre' => 'Microondas', 'icono' => '🍽️', 'potencia_nominal_w' => 1100, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Computadora', 'icono' => '💻', 'potencia_nominal_w' => 300, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Cafetera', 'icono' => '☕', 'potencia_nominal_w' => 900, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Congelador', 'icono' => '🧊', 'potencia_nominal_w' => 200, 'potencia_arranque_w' => 600, 'es_motor' => true],
            ['nombre' => 'Herramienta Eléctrica', 'icono' => '🛠️', 'potencia_nominal_w' => 1000, 'potencia_arranque_w' => 2500, 'es_motor' => true],
            ['nombre' => 'Cocina de Inducción', 'icono' => '🍳', 'potencia_nominal_w' => 1800, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Router / Módem', 'icono' => '📶', 'potencia_nominal_w' => 15, 'potencia_arranque_w' => null, 'es_motor' => false],
            ['nombre' => 'Ventilador', 'icono' => '🌀', 'potencia_nominal_w' => 75, 'potencia_arranque_w' => 150, 'es_motor' => true],
            ['nombre' => 'Cargador de Celular', 'icono' => '🔌', 'potencia_nominal_w' => 10, 'potencia_arranque_w' => null, 'es_motor' => false],
        ];

        $equiposModel = [];
        foreach ($equipos as $data) {
            $equiposModel[$data['nombre']] = EquipoElectrico::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        // ---------- CATÁLOGO DEFAULT POR ENTORNO ----------
        // [nombre_equipo => [cantidad_defecto, seleccionado_defecto, orden]]
        // Los aires de 18k, 24k y 36k BTU están disponibles en todos los entornos (cantidad 0, sin seleccionar).
        $catalogoPorEntorno = [
            'residencial' => [
                'Refrigeradora' => [1, true, 1],
                'Aire Acond. 12k BTU' => [0, false, 2],
                'Aire Acond. 18k BTU' => [0, false, 3],
                'Aire Acond. 24k BTU' => [0, false, 4],
                'Aire Acond. 36k BTU' => [0, false, 5],
                'Luces LED' => [10, true, 6],
                'TV' => [1, false, 7],
                'Lavadora' => [0, false, 8],
                'Microondas' => [0, false, 9],
                'Computadora' => [0, false, 10],
                'Router / Módem' => [1, true, 11],
                'Bomba de Agua Residencial' => [0, false, 12],
            ],
            'comercial' => [
                'Luces LED' => [20, true, 1],
                'Aire Acond. 12k BTU' => [2, true, 2],
                'Aire Acond. 18k BTU' => [0, false, 3],
                'Aire Acond. 24k BTU' => [0, false, 4],
                'Aire Acond. 36k BTU' => [0, false, 5],
                'Computadora' => [4, true, 6],
                'Refrigeradora' => [1, true, 7],
                'Cafetera' => [1, false, 8],
                'Router / Módem' => [2, true, 9],
            ],
            'industrial' => [
                'Herramienta Eléctrica' => [3, true, 1],
                'Bomba de Agua' => [1, true, 2],
                'Luces LED' => [30, true, 3],
                'Ventilador' => [4, false, 4],
                'Aire Acond. 18k BTU' => [0, false, 5],
                'Aire Acond. 24k BTU' => [0, false, 6],
                'Aire Acond. 36k BTU' => [0, false, 7],
            ],
            'eventos' => [
                'Luces LED' => [40, true, 1],
                'Ventilador' => [4, false, 2],
                'Cocina de Inducción' => [1, false, 3],
                'Cargador de Celular' => [10, false, 4],
                'Aire Acond. 18k BTU' => [0, false, 5],
                'Aire Acond. 24k BTU' => [0, false, 6],
                'Aire Acond. 36k BTU' => [0, false, 7],
            ],
            'campamento' => [
                'Luces LED' => [6, true, 1],
                'Congelador' => [1, false, 2],
                'Bomba de Agua' => [1, true, 3],
                'Cargador de Celular' => [4, true, 4],
                'Aire Acond. 18k BTU' => [0, false, 5],
                'Aire Acond. 24k BTU' => [0, false, 6],
                'Aire Acond. 36k BTU' => [0, false, 7],
            ],
        ];

        foreach ($catalogoPorEntorno as $slug => $items) {
            $entorno = Entorno::where('slug', $slug)->first();

            $sync = [];
            foreach ($items as $nombreEquipo => [$cantidad, $seleccionado, $orden]) {
                $sync[$equiposModel[$nombreEquipo]->id] = [
                    'cantidad_defecto' => $cantidad,
                    'seleccionado_defecto' => $seleccionado,
                    'orden' => $orden,
                ];
            }

            $entorno->equipos()->sync($sync);
        }
    }
}