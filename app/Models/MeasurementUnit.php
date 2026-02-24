<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    protected $fillable = ['code', 'name', 'description', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
