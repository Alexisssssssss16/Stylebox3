<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Product;
use App\Models\Talla;
use App\Models\ProductoTalla;
use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Nuevos Colores de Tendencia
        $colors = [
            ['name' => 'Celeste Sky', 'hex_code' => '#87CEEB'],
            ['name' => 'Coral Soft', 'hex_code' => '#FF7F50'],
            ['name' => 'Marino Deep', 'hex_code' => '#000080'],
            ['name' => 'Esmeralda Luxe', 'hex_code' => '#50C878'],
            ['name' => 'Lila Pastel', 'hex_code' => '#C8A2C8'],
            ['name' => 'Terracota', 'hex_code' => '#E2725B'],
            ['name' => 'Ocre Gold', 'hex_code' => '#CC9900'],
            ['name' => 'Gris Melange', 'hex_code' => '#808080'],
            ['name' => 'Rosa Palo', 'hex_code' => '#E6A8D7'],
            ['name' => 'Verde Militar', 'hex_code' => '#4B5320'],
        ];

        foreach ($colors as $colorData) {
            Color::updateOrCreate(['name' => $colorData['name']], $colorData);
        }

        $allColors = Color::all();
        $unit = MeasurementUnit::first() ?? MeasurementUnit::create(['name' => 'Unidad', 'code' => 'UND', 'status' => true]);

        $brands = ['Hypnotic', 'Sybilas', 'Basement', 'University Club', 'Orange Blue'];
        $categories = ['Blusas', 'Polos', 'Pantalones', 'Vestidos', 'Casacas', 'Accesorios'];
        $genders = ['Mujer', 'Hombre', 'Unisex', 'Niño/a'];

        // 2. Nuevas Prendas (20 items)
        $items = [
            ['name' => 'Blusa Volantes Seda', 'cat' => 'Blusas', 'gender' => 'Mujer'],
            ['name' => 'Polo Pique Premium', 'cat' => 'Polos', 'gender' => 'Hombre'],
            ['name' => 'Jeans Skinny High', 'cat' => 'Pantalones', 'gender' => 'Mujer'],
            ['name' => 'Vestido Midi Floral', 'cat' => 'Vestidos', 'gender' => 'Mujer'],
            ['name' => 'Casaca Bomber Urban', 'cat' => 'Casacas', 'gender' => 'Unisex'],
            ['name' => 'Chomba Tejida Soft', 'cat' => 'Polos', 'gender' => 'Mujer'],
            ['name' => 'Pantalón Chino Slim', 'cat' => 'Pantalones', 'gender' => 'Hombre'],
            ['name' => 'Blusa Denim Light', 'cat' => 'Blusas', 'gender' => 'Mujer'],
            ['name' => 'Hoodie Oversize Art', 'cat' => 'Polos', 'gender' => 'Unisex'],
            ['name' => 'Falda Plisada Shine', 'cat' => 'Vestidos', 'gender' => 'Mujer'],
            ['name' => 'Camisa Oxford Classic', 'cat' => 'Blusas', 'gender' => 'Hombre'],
            ['name' => 'Short Denim Destroy', 'cat' => 'Pantalones', 'gender' => 'Mujer'],
            ['name' => 'Cardigan Largo Knit', 'cat' => 'Casacas', 'gender' => 'Mujer'],
            ['name' => 'T-Shirt Graphic Eco', 'cat' => 'Polos', 'gender' => 'Niño/a'],
            ['name' => 'Leggings Sport High', 'cat' => 'Pantalones', 'gender' => 'Mujer'],
            ['name' => 'Parka Impermeable Rain', 'cat' => 'Casacas', 'gender' => 'Unisex'],
            ['name' => 'Top Crop Rib', 'cat' => 'Blusas', 'gender' => 'Mujer'],
            ['name' => 'Bermuda Cargo Trail', 'cat' => 'Pantalones', 'gender' => 'Hombre'],
            ['name' => 'Bufanda Oversize Wool', 'cat' => 'Accesorios', 'gender' => 'Unisex'],
            ['name' => 'Gorro Beanie Simple', 'cat' => 'Accesorios', 'gender' => 'Unisex'],
        ];

        foreach ($items as $index => $item) {
            $price = rand(49, 199);
            $cost = $price * 0.4;
            $brand = $brands[array_rand($brands)];
            $code = strtoupper(substr($item['cat'], 0, 2)) . '-' . Str::random(5);

            $product = Product::create([
                'code' => $code,
                'name' => $item['name'],
                'category' => $item['cat'],
                'brand' => $brand,
                'gender' => $item['gender'],
                'description' => "Excelente prenda de la colección 2026 de {$brand}. Calidad garantizada.",
                'cost' => $cost,
                'price' => $price,
                'stock' => 0, // Se calculará con las variantes
                'measurement_unit_id' => $unit->id,
                'status' => true,
            ]);

            // Asignar variantes (Color + Talla) random
            $tipoTalla = in_array($item['cat'], ['Pantalones', 'Faldas']) ? 'inferior' : 'superior';
            $tallas = Talla::where('tipo', $tipoTalla)->get();
            $coloresSeleccionados = $allColors->random(rand(2, 4));

            foreach ($coloresSeleccionados as $color) {
                foreach ($tallas->random(rand(2, 3)) as $talla) {
                    $stock = rand(5, 50);
                    ProductoTalla::create([
                        'producto_id' => $product->id,
                        'talla_id' => $talla->id,
                        'color_id' => $color->id,
                        'stock' => $stock,
                        'activo' => true,
                    ]);
                }
            }
            $product->sincronizarStock();
        }
    }
}
