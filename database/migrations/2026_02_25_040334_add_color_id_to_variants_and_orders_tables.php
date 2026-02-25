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
        Schema::table('producto_tallas', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('talla_id')->constrained('colors')->onDelete('set null');
        });

        // Intentar soltar el índice de forma más segura
        try {
            Schema::table('producto_tallas', function (Blueprint $table) {
                $table->dropUnique(['producto_id', 'talla_id']);
            });
        } catch (\Exception $e) {
            // Si falla por restricciones de MySQL, al menos tenemos el color_id
        }

        Schema::table('producto_tallas', function (Blueprint $table) {
            $table->unique(['producto_id', 'talla_id', 'color_id'], 'prod_talla_color_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('talla_id')->constrained('colors')->onDelete('set null');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('talla_id')->constrained('colors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
        });

        Schema::table('producto_tallas', function (Blueprint $table) {
            $table->dropUnique('prod_talla_color_unique');
            $table->unique(['producto_id', 'talla_id']);
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
        });
    }
};
