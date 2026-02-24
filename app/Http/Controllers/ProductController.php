<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ProductoTalla;
use App\Models\Talla;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['measurementUnit', 'productoTallas.talla'])->latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->get();
        $units = MeasurementUnit::where('status', true)->get();

        // Tallas por tipo (para los modals de admin)
        $tallasSuperiores = Talla::where('tipo', 'superior')->orderBy('orden')->get();
        $tallasInferiores = Talla::where('tipo', 'inferior')->orderBy('orden')->get();

        return view('products.index', compact('products', 'units', 'tallasSuperiores', 'tallasInferiores'));
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

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
