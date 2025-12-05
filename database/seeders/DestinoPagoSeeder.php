<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matrícula, Mensualidad, Inscripción, Otros
        $destinosPago = [
            'BCP',
            'BBVA',
            'Caja Piura',
            'Caja local',
            'Otros',
        ];
        foreach ($destinosPago as $destino) {
            \App\Models\DestinoPago::create(['nombre' => $destino]);
        }
    }
}
