<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        // 1. Featured Products for Hero Carousel (up to 5, random order)
        $featuredProducts = Product::active()
            ->inStock()
            ->with('measurementUnit')
            ->inRandomOrder()
            ->take(5)
            ->get();

        // 2. All active products with stock for the main grid (paginated)
        $products = Product::active()
            ->inStock()
            ->with('measurementUnit')
            ->latest()
            ->paginate(12);

        return view('catalogo', compact('featuredProducts', 'products'));
    }
}
