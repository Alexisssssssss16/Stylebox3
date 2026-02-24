<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tallas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10);           // S, M, L, XL, XXL, 28, 30...
            $table->enum('tipo', ['superior', 'inferior']); // superior=polos/camisas/vestidos, inferior=pantalones
            $table->unsignedTinyInteger('orden')->default(0); // Para ordenar en la UI
            $table->timestamps();

            $table->unique(['nombre', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tallas');
    }
};
