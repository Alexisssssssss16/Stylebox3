<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Limpiar tabla antes de insertar
        DB::table('measurement_units')->truncate();

        $units = [
            ['code' => 'NIU', 'name' => 'UNIDAD', 'description' => 'UNIDAD (BIENES)'],
            ['code' => 'ZZ', 'name' => 'SERVICIO', 'description' => 'UNIDAD (SERVICIOS)'],
            ['code' => 'H87', 'name' => 'PIEZA', 'description' => 'PIEZA'],
            ['code' => 'DZN', 'name' => 'DOCENA', 'description' => 'DOCENA'],
            ['code' => 'PR', 'name' => 'PAR', 'description' => 'PAR'],
            ['code' => 'MTR', 'name' => 'METRO', 'description' => 'METRO'],
            ['code' => 'KGM', 'name' => 'KILOGRAMO', 'description' => 'KILOGRAMO'],
            ['code' => 'SET', 'name' => 'JUEGO', 'description' => 'JUEGO (CONJUNTO)'],
            ['code' => 'BX', 'name' => 'CAJA', 'description' => 'CAJA'],
            ['code' => 'CT', 'name' => 'CARTON', 'description' => 'CARTON'],
        ];

        foreach ($units as $unit) {
            DB::table('measurement_units')->insert([
                'code' => $unit['code'],
                'name' => $unit['name'],
                'description' => $unit['description'],
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
