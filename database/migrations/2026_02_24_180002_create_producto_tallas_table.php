<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('producto_tallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('talla_id')
                ->constrained('tallas')
                ->onDelete('cascade');
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('activo')->default(true);
            $table->decimal('precio_extra', 8, 2)->nullable(); // Para futura escalabilidad (color+talla)
            $table->timestamps();

            $table->unique(['producto_id', 'talla_id']); // No duplicar talla por producto
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_tallas');
    }
};
