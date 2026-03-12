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
        Schema::table('pago', function (Blueprint $table) {
            $table->foreign('destino_pago_id')
                ->references('id')
                ->on('destino_pago')
                ->restrictOnDelete();
            $table->foreign('medio_pago_id')
                ->references('id')
                ->on('medio_pago')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropForeign(['destino_pago_id', 'medio_pago_id']);
        });
    }
};
