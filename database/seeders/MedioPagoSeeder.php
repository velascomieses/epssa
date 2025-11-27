<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedioPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Efectivo, Transferencia, Yape, Plin, Tarjeta de crédito, Tarjeta de débito
        $mediosPago = [
            'Efectivo',
            'Transferencia',
            'Yape',
            'Plin',
            'Tarjeta de crédito',
            'Tarjeta de débito',
        ];
        foreach ($mediosPago as $medio) {
            \App\Models\MedioPago::create(['nombre' => $medio]);
        }
    }
}
