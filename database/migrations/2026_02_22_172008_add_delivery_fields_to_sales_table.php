<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('delivery')->default(false)->after('status');
            $table->string('delivery_address')->nullable()->after('delivery');
            $table->string('delivery_district')->nullable()->after('delivery_address');
            $table->string('delivery_reference')->nullable()->after('delivery_district');
            $table->decimal('delivery_cost', 10, 2)->default(0)->after('delivery_reference');
            // buyer_id: the authenticated user buying (separate from user_id which is the seller/admin)
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null')->after('client_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropColumn([
                'delivery',
                'delivery_address',
                'delivery_district',
                'delivery_reference',
                'delivery_cost',
                'buyer_id',
            ]);
        });
    }
};
