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
        Schema::create('cartera_riesgo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('contrato_id')->nullable();
            $table->date('fecha_contrato')->nullable();
            $table->integer('titular_id')->nullable();
            $table->integer('personal_id')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->decimal('monto_capital', 16, 2);
            $table->decimal('monto_pendiente', 16, 2);
            $table->integer('num_cuotas_vencidas')->default(0);
            $table->date('ultima_fecha_pago')->nullable();
            $table->char('categoria_riesgo', 30);
            $table->integer('tipo_contrato_id')->nullable();
            $table->decimal('total_contrato', 16, 2);
            $table->date('fecha_evaluacion');
            $table->timestamps();

            $table->foreign('contrato_id')->references('id')->on('contrato');
            $table->foreign('titular_id')->references('id')->on('persona');
            $table->foreign('personal_id')->references('id')->on('personal');
            $table->foreign('tipo_contrato_id')->references('id')->on('tipo_contrato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartera_riesgo');
    }
};
