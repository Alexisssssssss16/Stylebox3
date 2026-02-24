<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Services\CartService;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected SaleService $saleService,
    ) {
    }

    /**
     * GET /checkout
     * Show the checkout page. Redirects to cart if empty.
     */
    public function show()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('info', 'Tu carrito está vacío. Agrega productos antes de continuar.');
        }

        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $paymentMethods = PaymentMethod::where('status', true)->get();

        return view('shop.checkout', compact('items', 'subtotal', 'paymentMethods'));
    }

    /**
     * POST /checkout
     * Validate form, persist the sale, clear cart, redirect to confirmation.
     */
    public function store(CheckoutRequest $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $validated = $request->validated();

        // Build cart array for SaleService
        $cartItems = $this->cart->items()->map(fn($item) => [
            'id' => $item['id'],
            'talla_id' => $item['talla_id'] ?? null,
            'quantity' => $item['quantity'],
        ])->values()->toArray();

        try {
            $sale = $this->saleService->createSale([
                'buyer_id' => Auth::id(),
                'user_id' => Auth::id(),  // buyer acts as "seller" in self-service checkout
                'cart' => $cartItems,
                'payments' => [
                    [
                        'method_id' => $validated['payment_method_id'],
                        'amount' => $this->cart->subtotal(),
                    ]
                ],
                // Pickup / delivery-agent fields
                'tipo_entrega' => $validated['tipo_entrega'],
                'nombre_repartidor' => $validated['nombre_repartidor'] ?? null,
                'dni_repartidor' => $validated['dni_repartidor'] ?? null,
                'telefono_repartidor' => $validated['telefono_repartidor'] ?? null,
                'empresa_delivery' => $validated['empresa_delivery'] ?? null,
                'placa_vehiculo' => $validated['placa_vehiculo'] ?? null,
            ]);

            $this->cart->clear();

            return redirect()->route('checkout.confirmation', $sale)
                ->with('success', '¡Pedido registrado con éxito!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * GET /checkout/{sale}/confirmacion
     * Order confirmation page (buyer must own the sale).
     */
    public function confirmation(Sale $sale)
    {
        // Security: only the buyer can see their own confirmation
        if ($sale->buyer_id !== Auth::id()) {
            abort(403);
        }

        $sale->load('details.product', 'payments.paymentMethod');

        return view('shop.order-confirmation', compact('sale'));
    }

    /**
     * GET /checkout/{sale}/boleta
     * Show the formal receipt (Boleta) for printing.
     */
    public function boleta(Sale $sale)
    {
        // Security check: only the buyer can see their own boleta
        if ($sale->buyer_id !== Auth::id()) {
            abort(403);
        }

        $sale->load('details.product', 'buyer', 'client', 'payments.paymentMethod');

        return view('shop.boleta', compact('sale'));
    }

    /**
     * POST /admin/orders/{sale}/estado
     * Admin/Vendedor: advance the order status through the state machine.
     */
    public function updateEstado(Request $request, Sale $sale)
    {
        $request->validate([
            'estado_pedido' => ['required', 'in:pagado,listo_recojo,recogido'],
        ]);

        try {
            $this->saleService->cambiarEstado($sale, $request->estado_pedido);

            return back()->with(
                'success',
                "Pedido #{$sale->id}: estado actualizado a «{$sale->fresh()->estadoPedidoLabel()}»."
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
