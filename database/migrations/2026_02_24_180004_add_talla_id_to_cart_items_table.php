<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'talla_id')) {
                $table->foreignId('talla_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('tallas')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'talla_id')) {
                $table->dropForeign(['talla_id']);
                $table->dropColumn('talla_id');
            }
        });
    }
};
