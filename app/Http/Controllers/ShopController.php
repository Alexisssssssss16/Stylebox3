<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Base query: only active products
        $query = Product::active()->with(['measurementUnit', 'productoTallas.talla', 'productoTallas.color']);

        // Search by name or code
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by size (talla)
        if ($request->filled('talla')) {
            $query->whereHas('productoTallas', function ($q) use ($request) {
                $q->where('talla_id', $request->talla)->activas()->conStock();
            });
        }

        // Filter by color
        if ($request->filled('color')) {
            $query->whereHas('productoTallas', function ($q) use ($request) {
                $q->where('color_id', $request->color)->activas()->conStock();
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Ensure we only show products that HAVE stock in the filtered variants 
        // Or if no filters, ensure it has at least some stock somewhere
        $query->where('stock', '>', 0);

        $products = $query->orderBy('created_at', 'desc')->paginate(24);

        // Data for filters dropdowns (Oechsle style)
        $categories = Product::active()->distinct()->pluck('category')->filter()->sort()->values();
        $tallas = \App\Models\Talla::orderBy('id')->get();
        $colors = \App\Models\Color::orderBy('name')->get();

        return view('shop.index', compact('products', 'categories', 'tallas', 'colors'));
    }

    public function show(Product $product)
    {
        // Abort if the product is inactive or out of stock
        if (!$product->status || $product->stock <= 0) {
            abort(404);
        }
        return view('shop.show', compact('product'));
    }

    public function feed()
    {
        // Fetch active products with stock, newest first (TikTok-style feed)
        $products = Product::active()
            ->inStock()
            ->with('measurementUnit')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('shop.feed', compact('products'));
    }
}
