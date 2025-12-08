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
        Schema::table('producto', function (Blueprint $table) {
            $table->boolean('es_serializado')->default(false)->after('precio_unitario');
            $table->timestamps();
            $table->foreignId('user_audit_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropForeign(['user_audit_id']);
            $table->dropColumn(['es_serializado','user_audit_id', 'created_at', 'updated_at']);
        });
    }
};
