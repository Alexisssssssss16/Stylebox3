<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * CartService — Hybrid shopping cart (Session for guests, DB for Auth).
 */
class CartService
{
    const SESSION_KEY = 'stylebox_cart';

    /** Get all cart items as a collection. */
    public function items(): \Illuminate\Support\Collection
    {
        if (Auth::check()) {
            return CartItem::with(['product', 'talla'])
                ->where('user_id', Auth::id())
                ->get()
                ->map(fn($item) => [
                    'id' => $item->product_id,
                    'talla_id' => $item->talla_id,
                    'name' => $item->product->name,
                    'talla' => $item->talla?->nombre,
                    'price' => (float) ($item->product->price + ($item->productoTalla?->precio_extra ?? 0)),
                    'image' => $item->product->image,
                    'quantity' => $item->quantity,
                    'stock' => $item->talla_id
                        ? $item->product->productoTallas()->where('talla_id', $item->talla_id)->first()?->stock ?? 0
                        : $item->product->stock,
                ]);
        }

        return collect(Session::get(self::SESSION_KEY, []));
    }

    /** Add or increment a product in the cart. */
    public function add(int $productId, string $name, float $price, int $stock, ?string $image, int $quantity = 1, ?int $tallaId = null, ?string $tallaNombre = null): void
    {
        $key = $tallaId ? "{$productId}_{$tallaId}" : (string) $productId;

        if (Auth::check()) {
            $item = CartItem::firstOrNew([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'talla_id' => $tallaId,
            ]);
            $newQty = ($item->exists ? $item->quantity : 0) + $quantity;
            $item->quantity = min($newQty, $stock);
            $item->save();
            return;
        }

        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$key])) {
            $newQty = $cart[$key]['quantity'] + $quantity;
            $cart[$key]['quantity'] = min($newQty, $stock);
        } else {
            $cart[$key] = [
                'id' => $productId,
                'talla_id' => $tallaId,
                'talla' => $tallaNombre,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => min($quantity, $stock),
                'stock' => $stock,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    /** Set an explicit quantity for a cart item. */
    public function update(int $productId, int $quantity, int $stock, ?int $tallaId = null): bool
    {
        $key = $tallaId ? "{$productId}_{$tallaId}" : (string) $productId;

        if (Auth::check()) {
            $item = CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('talla_id', $tallaId)
                ->first();

            if (!$item)
                return false;

            if ($quantity <= 0) {
                $item->delete();
                return true;
            }

            $item->update(['quantity' => min($quantity, $stock)]);
            return true;
        }

        $cart = Session::get(self::SESSION_KEY, []);

        if (!isset($cart[$key])) {
            return false;
        }

        if ($quantity <= 0) {
            $this->remove($productId, $tallaId);
            return true;
        }

        $cart[$key]['quantity'] = min($quantity, $stock);
        Session::put(self::SESSION_KEY, $cart);
        return true;
    }

    /** Remove a product from the cart. */
    public function remove(int $productId, ?int $tallaId = null): void
    {
        $key = $tallaId ? "{$productId}_{$tallaId}" : (string) $productId;

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('talla_id', $tallaId)
                ->delete();
            return;
        }

        $cart = Session::get(self::SESSION_KEY, []);
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);
    }

    /** Empty the cart. */
    public function clear(): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
            // Also clear session just in case
        }
        Session::forget(self::SESSION_KEY);
    }

    /** Sync session cart to DB (call after login). */
    public function syncSessionToDb(): void
    {
        if (!Auth::check())
            return;

        $sessionCart = Session::get(self::SESSION_KEY, []);
        foreach ($sessionCart as $productId => $itemData) {
            $product = Product::find($productId);
            if ($product) {
                $this->add(
                    $productId,
                    $product->name,
                    (float) $product->price,
                    $product->stock,
                    $product->image,
                    $itemData['quantity']
                );
            }
        }

        Session::forget(self::SESSION_KEY);
    }

    /** Subtotal of all items (no delivery). */
    public function subtotal(): float
    {
        return (float) $this->items()->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    /** Total number of items in cart. */
    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /** Whether the cart is empty. */
    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }
}
