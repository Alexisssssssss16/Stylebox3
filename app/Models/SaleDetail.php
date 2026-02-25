<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'talla_id',
        'color_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
