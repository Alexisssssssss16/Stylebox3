<?php

namespace Database\Seeders;

use App\Models\Talla;
use Illuminate\Database\Seeder;

class TallaSeeder extends Seeder
{
    public function run(): void
    {
        // Tallas SUPERIORES: Polos, Camisas, Vestidos
        $superiores = ['S', 'M', 'L', 'XL', 'XXL'];
        foreach ($superiores as $orden => $nombre) {
            Talla::firstOrCreate(
                ['nombre' => $nombre, 'tipo' => 'superior'],
                ['orden' => $orden]
            );
        }

        // Tallas INFERIORES: Pantalones
        $inferiores = ['28', '30', '32', '34', '36', '38', '40'];
        foreach ($inferiores as $orden => $nombre) {
            Talla::firstOrCreate(
                ['nombre' => $nombre, 'tipo' => 'inferior'],
                ['orden' => $orden]
            );
        }

        $this->command->info('✓ Tallas creadas: 5 superiores + 7 inferiores');
    }
}
