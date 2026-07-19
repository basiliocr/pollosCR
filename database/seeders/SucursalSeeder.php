<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Centro'],
            ['direccion' => 'Av. 6 de Marzo', 'activa' => true]
        );

        Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Sur'],
            ['direccion' => 'Por definir', 'activa' => true]
        );
    }
}