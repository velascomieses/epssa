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
        Schema::create('producto_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('producto')->restrictOnDelete();
            $table->string('numero_serie', 100)->unique();
            $table->foreignId('almacen_id')->constrained('almacen')->restrictOnDelete();
            $table->string('estado', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_item');
    }
};
