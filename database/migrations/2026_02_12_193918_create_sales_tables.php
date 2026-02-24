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
        // 1. Payment Methods (Efectivo, Tarjeta, Yape, etc.)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. Sales (Cabecera de Venta)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Seller
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total', 10, 2);
            $table->string('status')->default('completed'); // completed, canceled
            $table->timestamp('date');
            $table->timestamps();
        });

        // 3. Sale Details (Detalle de Productos)
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // 4. Sale Payments (Pagos Mixtos)
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable(); // Voucher code, operation ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_details');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('payment_methods');
    }
};
