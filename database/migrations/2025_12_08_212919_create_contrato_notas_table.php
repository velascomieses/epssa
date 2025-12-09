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
        Schema::create('contrato_nota', function (Blueprint $table) {
            $table->id();
            $table->integer('contrato_id')->nullable();
            $table->foreign('contrato_id')
                  ->references('id')
                  ->on('contrato')
                  ->restrictOnDelete();
            $table->text('nota')->nullable();
            $table->foreignId('user_audit_id')
                  ->nullable()
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_nota');
    }
};
