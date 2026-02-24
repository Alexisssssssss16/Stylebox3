<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart)
    {
    }

    /** Show cart page. */
    public function index()
    {
        return view('shop.cart', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    /** Add a product to the cart (POST, returns JSON). */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'talla_id' => 'nullable|integer|exists:tallas,id',
            'quantity' => 'sometimes|integer|min:1|max:100',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('status', true)
            ->firstOrFail();

        // Si el producto tiene tallas pero no se envió ninguna, error
        if ($product->usaTallas() && !$request->talla_id) {
            return response()->json(['success' => false, 'message' => "Por favor seleccione una talla para '{$product->name}'."], 422);
        }

        $talla = null;
        $maxStock = $product->stock;

        if ($request->talla_id) {
            $pt = $product->productoTallas()->where('talla_id', $request->talla_id)->with('talla')->first();
            if (!$pt || !$pt->activo) {
                return response()->json(['success' => false, 'message' => "La talla seleccionada no está disponible."], 422);
            }
            $talla = $pt->talla;
            $maxStock = $pt->stock;
        }

        if ($maxStock <= 0) {
            return response()->json(['success' => false, 'message' => "Sin stock disponible para esta selección."], 422);
        }

        $requestedQty = (int) ($request->quantity ?? 1);
        if ($requestedQty > $maxStock) {
            return response()->json(['success' => false, 'message' => "Sólo quedan {$maxStock} unidades disponibles."], 422);
        }

        $this->cart->add(
            $product->id,
            $product->name,
            (float) $product->price,
            $maxStock,
            $product->image,
            $requestedQty,
            $request->talla_id,
            $talla?->nombre
        );

        return response()->json([
            'success' => true,
            'message' => "'{$product->name}'" . ($talla ? " (Talla {$talla->nombre})" : "") . " agregado al carrito.",
            'count' => $this->cart->count(),
        ]);
    }

    /** Update quantity of a cart item (PATCH, returns JSON). */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
            'talla_id' => 'nullable|integer'
        ]);

        $product = Product::findOrFail($id);

        $maxStock = $product->stock;
        if ($request->talla_id) {
            $maxStock = $product->productoTallas()->where('talla_id', $request->talla_id)->first()?->stock ?? 0;
        }

        $this->cart->update($id, $request->quantity, $maxStock, $request->talla_id);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cart->subtotal(),
            'count' => $this->cart->count(),
        ]);
    }

    /** Remove an item from the cart (DELETE, returns JSON). */
    public function destroy(Request $request, int $id)
    {
        $this->cart->remove($id, $request->talla_id);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cart->subtotal(),
            'count' => $this->cart->count(),
        ]);
    }
}
