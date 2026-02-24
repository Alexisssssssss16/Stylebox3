<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'tipo', 'orden'];

    /**
     * Relación: una talla tiene muchos registros producto_talla.
     */
    public function productoTallas()
    {
        return $this->hasMany(ProductoTalla::class, 'talla_id');
    }

    /**
     * Categorías que usan tallas de tipo "superior".
     */
    public static function categoriasSuperiores(): array
    {
        return ['Polos', 'Camisas', 'Vestidos'];
    }

    /**
     * Categorías que usan tallas de tipo "inferior".
     */
    public static function categoriasInferiores(): array
    {
        return ['Pantalones'];
    }

    /**
     * Determina el tipo de talla según la categoría del producto.
     */
    public static function tipoParaCategoria(string $categoria): ?string
    {
        foreach (self::categoriasSuperiores() as $cat) {
            if (stripos($categoria, $cat) !== false)
                return 'superior';
        }
        foreach (self::categoriasInferiores() as $cat) {
            if (stripos($categoria, $cat) !== false)
                return 'inferior';
        }
        return null;
    }
}
