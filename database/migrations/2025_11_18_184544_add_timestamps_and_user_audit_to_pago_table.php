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
        Schema::table('pago', function (Blueprint $table) {
            $table->timestamps();
            $table->foreignId('user_audit_id')->nullable()
                ->constrained('users')
                ->restrictOnDelete();
            $table->integer('producto_id')
                ->nullable()
                ->after('contrato_id');
            $table->foreign('producto_id')
                ->references('id')
                ->on('producto')
                ->restrictOnDelete();
        });
        DB::statement('
                UPDATE pago p
                INNER JOIN (
                    SELECT pago_id, MIN(producto_id) as producto_id
                    FROM pago_producto
                    GROUP BY pago_id
                ) pp ON p.id = pp.pago_id
                SET p.producto_id = pp.producto_id
                WHERE p.tipo_ingreso = 2
            ');
        // Eliminar tabla pago_producto
        Schema::dropIfExists('pago_producto');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropForeign(['user_audit_id', 'producto_id']);
            $table->dropColumn(['user_audit_id', 'producto_id', 'created_at', 'updated_at']);
        });
    }
};
