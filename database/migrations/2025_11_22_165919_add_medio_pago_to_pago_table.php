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
            $table->foreignId('medio_pago_id')
                ->nullable()->after('tipo_comprobante_id')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropForeign(['medio_pago_id']);
            $table->dropColumn('medio_pago_id');
        });
    }
};
