<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Base query: only active products with stock
        $query = Product::active()->inStock()->with('measurementUnit');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Distinct categories for the filter dropdown
        $categories = Product::active()->inStock()->distinct()->pluck('category')->filter()->sort()->values();

        return view('shop.index', compact('products', 'categories'));
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
