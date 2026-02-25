<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'brand',
        'gender',
        'description',
        'image',
        'cost',
        'price',
        'stock',
        'measurement_unit_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /* ------------------------------------------------------------------ */
    /* Relaciones                                                           */
    /* ------------------------------------------------------------------ */

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    /** Tallas asignadas a este producto (con stock) */
    public function productoTallas()
    {
        return $this->hasMany(ProductoTalla::class, 'producto_id')
            ->with(['talla', 'color'])
            ->orderBy('talla_id');
    }

    /** Solo tallas activas */
    public function tallaActivas()
    {
        return $this->hasMany(ProductoTalla::class, 'producto_id')
            ->where('activo', true)
            ->with(['talla', 'color'])
            ->orderBy('talla_id');
    }

    /** Colores únicos de este producto */
    public function colors()
    {
        return $this->hasMany(ProductoTalla::class, 'producto_id')
            ->where('activo', true)
            ->whereNotNull('color_id')
            ->with('color')
            ->get()
            ->pluck('color')
            ->unique('id');
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                               */
    /* ------------------------------------------------------------------ */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /* ------------------------------------------------------------------ */
    /* Lógica de stock                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Stock calculado = suma del stock de todas las tallas activas.
     * Si el producto no tiene tallas asignadas, devuelve el stock del campo directo.
     */
    public function getStockCalculadoAttribute(): int
    {
        if ($this->productoTallas()->exists()) {
            return (int) $this->productoTallas()->where('activo', true)->sum('stock');
        }
        return (int) $this->stock;
    }

    /**
     * ¿Tiene stock disponible?
     * Acepta opcionalmente talla_id y color_id para verificar una variante específica.
     */
    public function hasStock(int $quantity, ?int $tallaId = null, ?int $colorId = null): bool
    {
        if ($tallaId || $colorId) {
            $query = $this->productoTallas();
            if ($tallaId)
                $query->where('talla_id', $tallaId);
            if ($colorId)
                $query->where('color_id', $colorId);

            $pt = $query->first();
            return $pt && $pt->stock >= $quantity;
        }

        return $this->stock >= $quantity;
    }

    /**
     * ¿El producto usa tallas (tiene al menos una talla asignada)?
     */
    public function usaTallas(): bool
    {
        return $this->productoTallas()->exists();
    }

    /**
     * Sincroniza el campo stock con el stock calculado desde las tallas.
     */
    public function sincronizarStock(): void
    {
        $this->update(['stock' => $this->getStockCalculadoAttribute()]);
    }
}
