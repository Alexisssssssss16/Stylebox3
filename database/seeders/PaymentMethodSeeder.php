<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Efectivo', 'status' => true],
            ['name' => 'Tarjeta', 'status' => true],
            ['name' => 'Yape', 'status' => true],
            ['name' => 'Plin', 'status' => true],
            ['name' => 'Mercado Pago', 'status' => true],
            ['name' => 'Transferencia', 'status' => true],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['name' => $method['name']],
                ['status' => $method['status'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
