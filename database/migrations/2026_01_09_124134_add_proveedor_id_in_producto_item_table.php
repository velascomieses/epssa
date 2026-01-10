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
        Schema::table('producto_item', function (Blueprint $table) {
            $table->integer('proveedor_id')->nullable()->after('id');
            $table->foreign('proveedor_id')->references('id')->on('persona')->restrictOnDelete();
        });
        DB::statement("
                UPDATE producto_item pi
                JOIN movimiento_item mi ON pi.id = mi.producto_item_id
                JOIN movimiento m ON mi.movimiento_id = m.id
                SET pi.proveedor_id = m.proveedor_id
                WHERE m.proveedor_id IS NOT NULL
                ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_item', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn('proveedor_id');
        });
    }
};
