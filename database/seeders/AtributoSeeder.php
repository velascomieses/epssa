<?php

namespace Database\Seeders;

use App\Models\Atributo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtributoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $atributos = [
            'Color',
            'Tamaño',
            'Material',
            'Marca',
            'Modelo',
            'Estilo',
            'Funcionalidad',
        ];
        foreach ($atributos as $atributo) {
            Atributo::firstOrCreate(['nombre' => $atributo]);
        }
    }
}
