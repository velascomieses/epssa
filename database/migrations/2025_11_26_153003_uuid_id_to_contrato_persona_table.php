<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear columna temporal
        Schema::table('contrato_persona', function (Blueprint $table) {
            $table->uuid('uuid_temp')->nullable()->after('id');
        });

        // 2. Poblar con UUIDs (ahora la columna YA existe)
        DB::table('contrato_persona')->orderBy('id')->chunk(1000, function ($personas) {
            foreach ($personas as $persona) {
                DB::table('contrato_persona')
                    ->where('id', $persona->id)
                    ->update(['uuid_temp' => (string) \Illuminate\Support\Str::uuid()]);
            }
        });

        // 3. Eliminar AUTO_INCREMENT de la columna id
        DB::statement('ALTER TABLE contrato_persona MODIFY id INTEGER NOT NULL');

        // 4. Ahora sí puedes eliminar la clave primaria y reemplazar
        Schema::table('contrato_persona', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
            $table->renameColumn('uuid_temp', 'id');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrato_persona', function (Blueprint $table) {
            //
        });
    }
};
