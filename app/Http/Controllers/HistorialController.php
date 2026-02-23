<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    /**
     * Display a listing of the buyer's purchases.
     */
    public function index()
    {
        $purchases = Auth::user()->purchases()
            ->with(['details.product'])
            ->paginate(10);

        return view('shop.historial.index', compact('purchases'));
    }

    /**
     * Display the specified purchase details.
     */
    public function show(Sale $sale)
    {
        // Security check
        if ($sale->buyer_id !== Auth::id()) {
            abort(403);
        }

        $sale->load(['details.product', 'payments.paymentMethod']);

        return view('shop.historial.show', compact('sale'));
    }

    /**
     * Repeat a past order: re-add items to cart.
     */
    public function repeatOrder(Sale $sale, CartService $cart)
    {
        // Security check
        if ($sale->buyer_id !== Auth::id()) {
            abort(403);
        }

        $itemsAdded = 0;
        foreach ($sale->details as $detail) {
            $product = $detail->product;
            if ($product && $product->stock > 0) {
                $qty = min($detail->quantity, $product->stock);
                $cart->add(
                    $product->id,
                    $product->name,
                    (float) $product->price,
                    $product->stock,
                    $product->image,
                    (int) $qty
                );
                $itemsAdded++;
            }
        }

        if ($itemsAdded === 0) {
            return redirect()->back()->with('error', 'No se pudieron agregar productos (sin stock).');
        }

        return redirect()->route('cart.index')->with('success', "Se han agregado {$itemsAdded} productos de tu pedido anterior.");
    }
}
