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
        Schema::create('settings', function (Blueprint $バランス) {
            $バランス->id();
            $バランス->string('key')->unique();
            $バランス->text('value')->nullable();
            $バランス->string('group')->default('general');
            $バランス->string('type')->default('string'); // string, text, boolean, integer
            $バランス->string('label')->nullable();
            $バランス->text('description')->nullable();
            $バランス->timestamps();
        });

        // Seed some default settings
        DB::table('settings')->insert([
            [
                'key' => 'store_name',
                'value' => 'StyleBox SaaS',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Nombre de la Tienda',
                'description' => 'El nombre que aparecerá en el encabezado y correos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_address',
                'value' => 'Dirección de ejemplo, Lima, Perú',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Dirección',
                'description' => 'Ubicación física de la tienda principal.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'store_phone',
                'value' => '+51 999 999 999',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Teléfono de Contacto',
                'description' => 'Número de WhatsApp o atención al cliente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'S/',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Símbolo de Moneda',
                'description' => 'Símbolo usado en los precios (ej. S/, $, €).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
