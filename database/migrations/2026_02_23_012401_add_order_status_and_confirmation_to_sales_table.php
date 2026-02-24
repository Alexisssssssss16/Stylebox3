<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('estado')->default('pendiente_pago')->after('canal_venta');
            $table->datetime('fecha_confirmacion_pago')->nullable()->after('estado');
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->after('fecha_confirmacion_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['confirmado_por']);
            $table->dropColumn(['estado', 'fecha_confirmacion_pago', 'confirmado_por']);
        });
    }
};
