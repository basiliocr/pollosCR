<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodoPago;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Efectivo', 'QR'] as $nombre) {
            MetodoPago::firstOrCreate(['nombre' => $nombre]);
        }
    }
}