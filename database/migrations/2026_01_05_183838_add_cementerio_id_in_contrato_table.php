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
        Schema::table('contrato', function (Blueprint $table) {
            $table->integer('cementerio_id')->nullable()->after('ubigeo_id');
            $table->foreign('cementerio_id')->references('id')->on('cementerio')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrato', function (Blueprint $table) {
            $table->dropForeign(['cementerio_id']);
            $table->dropColumn('cementerio_id');
        });
    }
};
