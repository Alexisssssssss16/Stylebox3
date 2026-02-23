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
            'quantity' => 'sometimes|integer|min:1|max:100',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('status', true)
            ->firstOrFail();

        if ($product->stock <= 0) {
            return response()->json(['success' => false, 'message' => "El producto '{$product->name}' no tiene stock disponible."], 422);
        }

        $requestedQty = (int) ($request->quantity ?? 1);
        if ($requestedQty > $product->stock) {
            return response()->json(['success' => false, 'message' => "Sólo quedan {$product->stock} unidades de '{$product->name}'."], 422);
        }

        $this->cart->add(
            $product->id,
            $product->name,
            (float) $product->price,
            $product->stock,
            $product->image,
            (int) ($request->quantity ?? 1)
        );

        return response()->json([
            'success' => true,
            'message' => "'{$product->name}' agregado al carrito.",
            'count' => $this->cart->count(),
        ]);
    }

    /** Update quantity of a cart item (PATCH, returns JSON). */
    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:100']);

        $product = Product::findOrFail($id);
        $this->cart->update($id, $request->quantity, $product->stock);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cart->subtotal(),
            'count' => $this->cart->count(),
        ]);
    }

    /** Remove an item from the cart (DELETE, returns JSON). */
    public function destroy(int $id)
    {
        $this->cart->remove($id);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cart->subtotal(),
            'count' => $this->cart->count(),
        ]);
    }
}
