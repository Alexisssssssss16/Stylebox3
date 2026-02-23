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
        'description',
        'image',
        'cost',
        'price',
        'stock',
        'measurement_unit_id',
        'status',
    ];

    /**
     * Automatic type casting.
     * status is stored as BOOLEAN (tinyint 0/1) in the DB.
     */
    protected $casts = [
        'status' => 'boolean',
        'price'  => 'decimal:2',
        'cost'   => 'decimal:2',
    ];

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    /**
     * Scope: only active products (status = true / 1).
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: only products with available stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
