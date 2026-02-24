<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTalla extends Model
{
    use HasFactory;

    protected $table = 'producto_tallas';

    protected $fillable = [
        'producto_id',
        'talla_id',
        'stock',
        'activo',
        'precio_extra',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_extra' => 'decimal:2',
        'stock' => 'integer',
    ];

    /* ------------------------------------------------------------------ */
    /* Relaciones                                                           */
    /* ------------------------------------------------------------------ */

    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                               */
    /* ------------------------------------------------------------------ */

    /** Solo tallas activas */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /** Solo tallas con stock disponible */
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /** Estado visual para la UI */
    public function getEstadoAttribute(): string
    {
        if ($this->stock === 0)
            return 'agotado';
        if ($this->stock < 5)
            return 'bajo';
        return 'disponible';
    }

    /** Verificar si hay suficiente stock para una cantidad */
    public function hasStock(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }
}
