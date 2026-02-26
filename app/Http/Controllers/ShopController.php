<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Base query: only active products
        $query = Product::active()->with(['measurementUnit', 'productoTallas.talla', 'productoTallas.color'])->withCount('productoTallas');

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

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
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

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(24);

        // Data for filters dropdowns
        $categoriesQuery = Product::active();
        if ($request->filled('gender')) {
            $categoriesQuery->where('gender', $request->gender);
        }
        $categories = $categoriesQuery->distinct()->pluck('category')->filter()->sort()->values();
        $tallas = \App\Models\Talla::orderBy('id')->get();
        $colors = \App\Models\Color::orderBy('name')->get();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand')->sort()->values();
        $genders = Product::active()->whereNotNull('gender')->where('gender', '!=', '')->distinct()->pluck('gender')->sort()->values();

        return view('shop.index', compact('products', 'categories', 'tallas', 'colors', 'brands', 'genders'));
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

    /**
     * Devuelve las tallas/colores de un producto para el modal de compra.
     */
    public function getSizes(Product $product)
    {
        $variants = $product->productoTallas()
            ->where('activo', true)
            ->with(['talla', 'color'])
            ->get();

        $grouped = $variants->groupBy('color_id')->map(function ($items) {
            $first = $items->first();
            return [
                'color_id' => $first->color_id,
                'color_nombre' => $first->color?->name ?? 'Estándar',
                'color_hex' => $first->color?->hex_code ?? '#ccc',
                'tallas' => $items->map(fn($pt) => [
                    'id' => $pt->talla_id,
                    'nombre' => $pt->talla->nombre,
                    'stock' => $pt->stock,
                ])->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'product_name' => $product->name,
            'product_price' => (float) $product->price,
            'colors' => $grouped
        ]);
    }
}
