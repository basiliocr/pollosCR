<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnidadMedida;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg', 'tipo' => 'peso', 'equivalencia_base' => 1000],
            ['nombre' => 'Gramo', 'abreviatura' => 'g', 'tipo' => 'peso', 'equivalencia_base' => 1],
            ['nombre' => 'Litro', 'abreviatura' => 'L', 'tipo' => 'volumen', 'equivalencia_base' => 1000],
            ['nombre' => 'Mililitro', 'abreviatura' => 'ml', 'tipo' => 'volumen', 'equivalencia_base' => 1],
            ['nombre' => 'Unidad', 'abreviatura' => 'u', 'tipo' => 'unidad', 'equivalencia_base' => 1],
        ];

        foreach ($unidades as $u) {
            UnidadMedida::firstOrCreate(['abreviatura' => $u['abreviatura']], $u);
        }
    }
}