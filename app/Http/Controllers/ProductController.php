<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ProductoTalla;
use App\Models\Talla;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Events\StockActualizadoEvent;
use App\Http\Controllers\DashboardStatsController;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['measurementUnit', 'productoTallas.talla', 'productoTallas.color'])->latest();

        // Búsqueda General (Código, Nombre, Categoría)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filtro por Marca
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filtro por Género
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filtro por Categoría
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtro por Color (a través de ProductoTalla)
        if ($request->filled('color')) {
            $query->whereHas('productoTallas', function ($q) use ($request) {
                $q->where('color_id', $request->color);
            });
        }

        // Filtro por Talla (a través de ProductoTalla)
        if ($request->filled('talla')) {
            $query->whereHas('productoTallas', function ($q) use ($request) {
                $q->where('talla_id', $request->talla);
            });
        }

        // Ordenamiento
        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'a_z' => $query->orderBy('name', 'asc'),
                'z_a' => $query->orderBy('name', 'desc'),
                'stock_low' => $query->orderBy('stock', 'asc'),
                'stock_high' => $query->orderBy('stock', 'desc'),
                default => $query->latest()
            };
        }

        $products = $query->paginate(15)->withQueryString();
        $units = MeasurementUnit::where('status', true)->get();
        $colors = Color::orderBy('name')->get();
        $tallas = Talla::orderBy('tipo')->orderBy('orden')->get();

        // Marcas y Categorías únicas para los filtros
        $uniqueBrands = Product::whereNotNull('brand')->distinct()->pluck('brand');
        $uniqueCategories = Product::whereNotNull('category')->distinct()->pluck('category');

        $tallasSuperiores = Talla::where('tipo', 'superior')->orderBy('orden')->get();
        $tallasInferiores = Talla::where('tipo', 'inferior')->orderBy('orden')->get();

        return view('products.index', compact(
            'products',
            'units',
            'tallasSuperiores',
            'tallasInferiores',
            'colors',
            'tallas',
            'uniqueBrands',
            'uniqueCategories'
        ));
    }

    public function create()
    {
        // Uses modal
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
            'status' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        // Asignar automáticamente las tallas según la categoría
        $tallaController = new ProductoTallaController();
        $tallaController->asignarTallasAutomaticas($product);

        // Broadcasting update
        $this->broadcastStockUpdate();

        return redirect()->route('products.index')
            ->with('success', 'Producto registrado exitosamente.');
    }

    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        // Uses modal
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
            'status' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $oldCategory = $product->category;
        $product->update($data);

        // Si la categoría cambió, asignar tallas de la nueva categoría
        if ($oldCategory !== $product->category && !$product->usaTallas()) {
            $tallaController = new ProductoTallaController();
            $tallaController->asignarTallasAutomaticas($product);
        }

        // Broadcasting update
        $this->broadcastStockUpdate();

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        // Broadcasting update
        $this->broadcastStockUpdate();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Helper para disparar el evento de stock actualizado al dashboard.
     */
    private function broadcastStockUpdate()
    {
        try {
            $statsController = new DashboardStatsController();
            $stats = $statsController->prepareAdminStatsPayload();
            broadcast(new StockActualizadoEvent($stats));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Stock broadcast failed: " . $e->getMessage());
        }
    }
}
