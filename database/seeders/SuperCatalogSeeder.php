<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Product;
use App\Models\Talla;
use App\Models\ProductoTalla;
use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuperCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar datos antiguos para evitar duplicados
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ProductoTalla::truncate();
        Product::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 2. Garantizar Unidad de Medida
        $unit = MeasurementUnit::firstOrCreate(
            ['code' => 'UND'],
            ['name' => 'Unidad', 'status' => true]
        );

        // 2. Definiciones del Catálogo (según prompt)
        $brands = [
            'SYBILAS',
            'NORTO',
            'SINGULAR',
            'URBAN FIT',
            'DUNKELVOLK STYLE',
            'STYLEBOX BASIC',
            'STREET MODE',
            'COTTON PLUS'
        ];

        $categories = [
            'Polos' => 'superior',
            'Camisas' => 'superior',
            'Pantalones' => 'inferior',
            'Vestidos' => 'superior',
            'Blusas' => 'superior',
            'Abrigos / Casacas' => 'superior',
            'Shorts' => 'inferior',
            'Buzos / Joggers' => 'inferior',
            'Poleras / Sudaderas' => 'superior'
        ];

        $colorData = [
            ['name' => 'Negro', 'hex' => '#000000'],
            ['name' => 'Blanco', 'hex' => '#FFFFFF'],
            ['name' => 'Azul Marino', 'hex' => '#000080'],
            ['name' => 'Beige', 'hex' => '#F5F5DC'],
            ['name' => 'Café', 'hex' => '#4B3621'],
            ['name' => 'Gris', 'hex' => '#808080'],
            ['name' => 'Rojo', 'hex' => '#FF0000'],
            ['name' => 'Verde Militar', 'hex' => '#4B5320'],
            ['name' => 'Celeste', 'hex' => '#87CEEB'],
            ['name' => 'Rosado', 'hex' => '#FFC0CB'],
            ['name' => 'Amarillo', 'hex' => '#FFFF00'],
            ['name' => 'Vino', 'hex' => '#722F37'],
        ];

        // Crear colores si no existen
        foreach ($colorData as $c) {
            Color::firstOrCreate(['name' => $c['name']], ['hex_code' => $c['hex']]);
        }
        $allColors = Color::all();

        // 3. Generación de 50 Productos
        $genders = ['Hombre', 'Mujer', 'Unisex'];

        for ($i = 1; $i <= 50; $i++) {
            $catName = array_rand($categories);
            $tipoTalla = $categories[$catName];
            $brand = $brands[array_rand($brands)];
            $gender = $genders[array_rand($genders)];

            // Ajuste de género lógico
            if ($catName == 'Vestidos' || $catName == 'Blusas')
                $gender = 'Mujer';

            $price = rand(45, 189) . '.90';
            $cost = number_format($price * 0.35, 2, '.', '');
            $code = 'STB-' . str_pad($i + 100, 4, '0', STR_PAD_LEFT);

            $name = $this->generateProductName($catName, $brand, $i);

            $product = Product::create([
                'code' => $code,
                'name' => $name,
                'category' => $catName,
                'brand' => $brand,
                'gender' => $gender,
                'description' => "Prenda de alta calidad de la marca {$brand}. Diseñada para brindar comodidad y estilo en cualquier ocasión. Colección StyleBox 2026.",
                'cost' => $cost,
                'price' => $price,
                'stock' => 0,
                'measurement_unit_id' => $unit->id,
                'status' => true,
                'image' => "products/" . Str::slug($catName) . "/" . Str::slug($name) . ".png"
            ]);

            // 4. Variaciones (3 a 8 colores)
            $selectedColors = $allColors->random(rand(3, 8));
            $tallasDisponibles = Talla::where('tipo', $tipoTalla)->get();

            foreach ($selectedColors as $color) {
                // Todas las tallas para cada color
                foreach ($tallasDisponibles as $talla) {
                    ProductoTalla::create([
                        'producto_id' => $product->id,
                        'talla_id' => $talla->id,
                        'color_id' => $color->id,
                        'stock' => rand(5, 30),
                        'activo' => true,
                    ]);
                }
            }

            $product->sincronizarStock();
        }
    }

    private function generateProductName($cat, $brand, $i)
    {
        $adjectives = ['Premium', 'Casual', 'Urban', 'Essence', 'Slim Fit', 'Oversize', 'Classic', 'Modern'];
        $adj = $adjectives[array_rand($adjectives)];

        $names = [
            'Polos' => ['Polo', 'T-Shirt', 'Camiseta'],
            'Camisas' => ['Camisa Oxford', 'Camisa Lino', 'Camisa Casual'],
            'Pantalones' => ['Jeans', 'Chino', 'Drill'],
            'Vestidos' => ['Vestido Midi', 'Vestido Gala', 'Vestido Boho'],
            'Blusas' => ['Blusa Seda', 'Top', 'Blusón'],
            'Abrigos / Casacas' => ['Casaca Bomber', 'Abrigo Lana', 'Parka'],
            'Shorts' => ['Bermuda', 'Short Denim', 'Short Cargo'],
            'Buzos / Joggers' => ['Jogger', 'Buzo Algodón', 'Pantalón Sport'],
            'Poleras / Sudaderas' => ['Hoodie', 'Sudadera', 'Polera Rib']
        ];

        $base = $names[$cat][array_rand($names[$cat])];
        return "{$base} {$adj} {$brand} " . ($i % 5 + 1);
    }
}
