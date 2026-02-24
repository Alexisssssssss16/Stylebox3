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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category'); // Polo, Pantalon, etc
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0); // Costo de compra/produccion
            $table->decimal('price', 10, 2); // Precio de venta
            $table->integer('stock')->default(0);
            $table->foreignId('measurement_unit_id')->constrained('measurement_units');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
