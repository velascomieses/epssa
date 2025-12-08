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
        Schema::create('producto_atributo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('producto_id')->nullable();
            $table->foreign('producto_id')
                ->references('id')->on('producto')->restrictOnDelete();
            $table->foreignId('atributo_id')
                ->references('id')->on('atributo')->restrictOnDelete();
            $table->string('valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_atributo');
    }
};
