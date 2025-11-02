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
        Schema::create('movimiento', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tipo_movimiento', 50);
            $table->date('fecha_movimiento');
            $table->integer('proveedor_id')->nullable();
            $table->bigInteger('almacen_origen_id')->nullable();
            $table->bigInteger('almacen_destino_id')->nullable();
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
