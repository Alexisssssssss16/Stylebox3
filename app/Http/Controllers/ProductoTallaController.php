<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductoTalla;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\StockActualizadoEvent;
use App\Http\Controllers\DashboardStatsController;

class ProductoTallaController extends Controller
{
    /**
     * Asigna automáticamente las tallas según la categoría del producto.
     * Llamado desde ProductController::store().
     */
    public function asignarTallasAutomaticas(Product $product): void
    {
        $tipo = Talla::tipoParaCategoria($product->category);
        if (!$tipo)
            return;

        $tallas = Talla::where('tipo', $tipo)->orderBy('orden')->get();
        foreach ($tallas as $talla) {
            ProductoTalla::firstOrCreate(
                ['producto_id' => $product->id, 'talla_id' => $talla->id],
                ['stock' => 0, 'activo' => true]
            );
        }
    }

    /**
     * GET /products/{product}/tallas/json
     * Devuelve las tallas del producto en JSON para el modal AJAX.
     */
    public function json(Product $product)
    {
        $tallas = ProductoTalla::with(['talla', 'color'])
            ->where('producto_id', $product->id)
            ->join('tallas', 'tallas.id', '=', 'producto_tallas.talla_id')
            ->orderBy('tallas.tipo')
            ->orderBy('tallas.orden')
            ->select('producto_tallas.*')
            ->get()
            ->map(fn($pt) => [
                'id' => $pt->id,
                'talla' => $pt->talla->nombre,
                'tipo' => $pt->talla->tipo,
                'color_id' => $pt->color_id,
                'color_name' => $pt->color?->name ?? 'N/A',
                'color_hex' => $pt->color?->hex_code,
                'stock' => $pt->stock,
                'activo' => (bool) $pt->activo,
            ]);

        return response()->json($tallas);
    }

    /**
     * POST /products/{product}/tallas
     * Crea/actualiza stock de una talla, o asigna tallas automáticamente si auto=true.
     */
    public function store(Request $request, Product $product)
    {
        // Asignación automática desde el modal cuando no hay tallas
        if ($request->boolean('auto')) {
            $this->asignarTallasAutomaticas($product);
            return response()->json(['success' => true, 'message' => 'Tallas asignadas automáticamente.']);
        }

        $request->validate([
            'talla_id' => 'required|exists:tallas,id',
            'color_id' => 'nullable|exists:colors,id',
            'stock' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);

        $pt = ProductoTalla::updateOrCreate(
            [
                'producto_id' => $product->id,
                'talla_id' => $request->talla_id,
                'color_id' => $request->color_id
            ],
            [
                'stock' => $request->stock,
                'activo' => $request->boolean('activo', true),
            ]
        );

        // Sincronizar stock total del producto
        $product->sincronizarStock();

        // Broadcasting update
        $this->broadcastStockUpdate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'pt' => $pt->load('talla'),
                'message' => "Talla {$pt->talla->nombre}: stock actualizado a {$pt->stock}.",
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', "Talla actualizada correctamente.");
    }

    /**
     * PUT /products/{product}/tallas/{productoTalla}
     * Editar stock/activo de una talla (Admin only).
     */
    public function update(Request $request, Product $product, ProductoTalla $producto_talla)
    {
        try {
            // Log para confirmar que llegamos aquí
            \Illuminate\Support\Facades\Log::info("Llego al update", [
                'prod_id' => $product->id,
                'pt_id' => $producto_talla->id
            ]);

            $request->validate([
                'stock' => 'required|integer|min:0',
                'activo' => 'sometimes|boolean',
            ]);

            $producto_talla->update([
                'stock' => $request->stock,
                'activo' => $request->boolean('activo', $producto_talla->activo),
            ]);

            // Sincronizar stock total del producto
            $product->sincronizarStock();

            // Broadcasting update
            $this->broadcastStockUpdate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'pt' => $producto_talla->fresh()->load(['talla', 'color']),
                    'message' => "Stock actualizado correctamente.",
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Stock de talla actualizado.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductoTalla update error: ' . $e->getMessage(), [
                'product_id' => $product->id ?? null,
                'productoTalla_id' => $producto_talla->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /products/{product}/tallas/{producto_talla}
     * Eliminar asignación de talla (Admin only).
     */
    public function destroy(Request $request, Product $product, ProductoTalla $producto_talla)
    {
        $producto_talla->delete();
        $product->sincronizarStock();

        // Broadcasting update
        $this->broadcastStockUpdate();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Talla eliminada.']);
        }

        return redirect()->route('products.index')->with('success', 'Talla eliminada del producto.');
    }

    /**
     * GET /inventario/tallas
     * Inventario general de todos los productos con sus tallas (Admin only).
     */
    public function inventario(Request $request)
    {
        $query = ProductoTalla::with(['producto', 'talla', 'color'])
            ->join('products', 'products.id', '=', 'producto_tallas.producto_id')
            ->join('tallas', 'tallas.id', '=', 'producto_tallas.talla_id')
            ->select('producto_tallas.*')
            ->orderBy('products.name')
            ->orderBy('tallas.tipo')
            ->orderBy('tallas.orden');

        if ($request->filled('producto')) {
            $query->where('products.name', 'like', '%' . $request->producto . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tallas.tipo', $request->tipo);
        }
        if ($request->filled('color')) {
            $query->where('producto_tallas.color_id', $request->color);
        }
        if ($request->filled('estado')) {
            match ($request->estado) {
                'agotado' => $query->where('producto_tallas.stock', 0),
                'bajo' => $query->whereBetween('producto_tallas.stock', [1, 4]),
                'disponible' => $query->where('producto_tallas.stock', '>=', 5),
                default => null,
            };
        }

        $registros = $query->get();
        $colors = \App\Models\Color::orderBy('name')->get();

        // Estadísticas
        $stats = [
            'total' => $registros->count(),
            'agotados' => $registros->where('stock', 0)->count(),
            'bajos' => $registros->whereBetween('stock', [1, 4])->count(),
            'disponibles' => $registros->where('stock', '>=', 5)->count(),
        ];

        return view('products.inventario-tallas', compact('registros', 'stats', 'colors'));
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
