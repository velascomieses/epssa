<?php

namespace Database\Seeders;

use App\Models\MedioPago;
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
            MedioPago::firstOrCreate(['nombre' => $medio]);
        }
    }
}
