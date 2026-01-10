<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contrato_nota', function (Blueprint $table) {
// 1. Agregar columna temporal UUID
            Schema::table('contrato_nota', function (Blueprint $table) {
                $table->uuid('uuid_temp')->after('id')->nullable();
            });

            // 2. Generar UUIDs para registros existentes
            DB::table('contrato_nota')->get()->each(function ($nota) {
                DB::table('contrato_nota')
                    ->where('id', $nota->id)
                    ->update(['uuid_temp' => Str::uuid()]);
            });
            // 3. Eliminar auto_increment del id actual
            DB::statement('ALTER TABLE contrato_nota MODIFY id INT NOT NULL');

            // 4. Eliminar clave primaria
            Schema::table('contrato_nota', function (Blueprint $table) {
                $table->dropPrimary(['id']);
            });

            // 5. Eliminar columna id
            Schema::table('contrato_nota', function (Blueprint $table) {
                $table->dropColumn('id');
            });

            // 6. Renombrar uuid_temp a id
            Schema::table('contrato_nota', function (Blueprint $table) {
                $table->renameColumn('uuid_temp', 'id');
            });

            // 7. Establecer nueva clave primaria
            Schema::table('contrato_nota', function (Blueprint $table) {
                $table->primary('id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrato_nota', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
        });

        Schema::table('contrato_nota', function (Blueprint $table) {
            $table->id();
        });
    }
};
