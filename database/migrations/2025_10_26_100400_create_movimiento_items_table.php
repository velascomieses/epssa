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
        Schema::create('movimiento_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('movimiento_id');
            $table->foreign('movimiento_id')->references('id')->on('movimiento')->restrictOnDelete();
            $table->integer('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('producto')->restrictOnDelete();
            $table->decimal('cantidad', 16, 2)->nullable();
            $table->uuid('producto_item_id')->nullable();
            $table->foreign('producto_item_id')->references('id')->on('producto_item')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_item');
    }
};
