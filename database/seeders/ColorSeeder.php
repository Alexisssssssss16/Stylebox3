<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Negro', 'hex_code' => '#000000'],
            ['name' => 'Blanco', 'hex_code' => '#FFFFFF'],
            ['name' => 'Rojo', 'hex_code' => '#FF0000'],
            ['name' => 'Azul', 'hex_code' => '#0000FF'],
            ['name' => 'Verde', 'hex_code' => '#008000'],
            ['name' => 'Gris', 'hex_code' => '#808080'],
            ['name' => 'Beige', 'hex_code' => '#F5F5DC'],
            ['name' => 'Azul Marino', 'hex_code' => '#000080'],
            ['name' => 'Burdeos', 'hex_code' => '#800000'],
            ['name' => 'Amarillo', 'hex_code' => '#FFFF00'],
            ['name' => 'Naranja', 'hex_code' => '#FFA500'],
            ['name' => 'Rosa', 'hex_code' => '#FFC0CB'],
            ['name' => 'Morado', 'hex_code' => '#800080'],
            ['name' => 'Cian', 'hex_code' => '#00FFFF'],
        ];

        foreach ($colors as $color) {
            \App\Models\Color::updateOrCreate(['name' => $color['name']], $color);
        }
    }
}
