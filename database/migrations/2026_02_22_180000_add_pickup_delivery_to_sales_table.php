<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tipo de entrega: 'recojo_tienda' o 'mi_delivery'
            $table->string('tipo_entrega')->default('recojo_tienda')->after('delivery_cost');

            // Datos del repartidor (solo cuando tipo_entrega = 'mi_delivery')
            $table->string('nombre_repartidor')->nullable()->after('tipo_entrega');
            $table->string('dni_repartidor')->nullable()->after('nombre_repartidor');
            $table->string('telefono_repartidor')->nullable()->after('dni_repartidor');
            $table->string('empresa_delivery')->nullable()->after('telefono_repartidor');
            $table->string('placa_vehiculo')->nullable()->after('empresa_delivery');

            // Estado del pedido (máquina de estados)
            // pendiente_pago → pagado → listo_recojo → recogido
            $table->string('estado_pedido')->default('pendiente_pago')->after('placa_vehiculo');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_entrega',
                'nombre_repartidor',
                'dni_repartidor',
                'telefono_repartidor',
                'empresa_delivery',
                'placa_vehiculo',
                'estado_pedido',
            ]);
        });
    }
};
