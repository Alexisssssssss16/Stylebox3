<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductoTalla;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    /**
     * Create a new sale with details, payments, and optional delivery data.
     *
     * Expected $data keys:
     *   cart[]              = [id, quantity]
     *   payments[]          = [method_id, amount, reference?]  (optional for checkout)
     *   client_id           (nullable)
     *   buyer_id            (nullable) — ID of the online buyer (comprador)
     *   delivery            (bool) legacy
     *   tipo_entrega        'recojo_tienda' | 'mi_delivery'
     *   nombre_repartidor   (nullable)
     *   dni_repartidor      (nullable)
     *   telefono_repartidor (nullable)
     *   empresa_delivery    (nullable)
     *   placa_vehiculo      (nullable)
     *   estado_pedido       default 'pendiente_pago'
     *
     * @throws Exception
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            // ── 1. Validate stock & compute product subtotals ───────────────
            $subtotal = 0;
            $cartItems = [];

            foreach ($data['cart'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);
                $tallaId = $item['talla_id'] ?? null;

                // Validar stock: por talla si aplica, o por producto si no tiene tallas
                if ($tallaId) {
                    $pt = ProductoTalla::where('producto_id', $product->id)
                        ->where('talla_id', $tallaId)
                        ->where('activo', true)
                        ->lockForUpdate()
                        ->first();

                    if (!$pt || $pt->stock < $item['quantity']) {
                        $tallaLabel = $pt ? $pt->talla->nombre : "desconocida";
                        throw new Exception("Stock insuficiente para: {$product->name} (talla {$tallaLabel})");
                    }
                } else {
                    if (!$product->hasStock($item['quantity'])) {
                        throw new Exception("Stock insuficiente para: {$product->name}");
                    }
                }

                $price = (float) $product->price;
                $itemSubtotal = $item['quantity'] * $price;
                $subtotal += $itemSubtotal;

                $cartItems[] = [
                    'product' => $product,
                    'talla_id' => $tallaId,
                    'pt' => $tallaId ? ($pt ?? null) : null,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // ── 2. Pickup orders: no delivery cost ──────────────────────────
            $deliveryCost = 0.0;  // No se calcula envío para recojo en tienda
            $grandTotal = $subtotal;

            // ── 3. Validate payment total if payments array provided ────────
            if (!empty($data['payments'])) {
                $totalPaid = collect($data['payments'])->sum('amount');
                if (abs($totalPaid - $grandTotal) > 0.01) {
                    throw new Exception(
                        "El monto pagado (S/ {$totalPaid}) no coincide con el total (S/ {$grandTotal})"
                    );
                }
            }

            // ── 4. Create Sale header ───────────────────────────────────────
            $tipoEntrega = $data['tipo_entrega'] ?? 'recojo_tienda';
            $esMiDelivery = $tipoEntrega === 'mi_delivery';
            $buyerId = $data['buyer_id'] ?? null;

            $sale = Sale::create([
                'canal_venta' => $buyerId ? 'ONLINE' : 'TIENDA',
                'numero_boleta' => $this->generateBoletaNumber(),
                'user_id' => $data['user_id'] ?? Auth::id(),
                'buyer_id' => $buyerId,
                'client_id' => $data['client_id'] ?? null,
                'total' => $subtotal,
                'status' => 'completed',
                'date' => now(),
                'delivery' => false,
                'delivery_cost' => 0,
                // Pickup fields
                'tipo_entrega' => $tipoEntrega,
                'nombre_repartidor' => $esMiDelivery ? ($data['nombre_repartidor'] ?? null) : null,
                'dni_repartidor' => $esMiDelivery ? ($data['dni_repartidor'] ?? null) : null,
                'telefono_repartidor' => $esMiDelivery ? ($data['telefono_repartidor'] ?? null) : null,
                'empresa_delivery' => $esMiDelivery ? ($data['empresa_delivery'] ?? null) : null,
                'placa_vehiculo' => $esMiDelivery ? ($data['placa_vehiculo'] ?? null) : null,
                'estado_pedido' => 'pendiente_pago',
            ]);

            // ── 5. Create details & decrement stock ─────────────────────────
            foreach ($cartItems as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'talla_id' => $item['talla_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Descontar stock: por talla si aplica, por producto si no tiene tallas
                if ($item['talla_id'] && $item['pt']) {
                    $item['pt']->decrement('stock', $item['quantity']);
                    // Desactivar talla si llega a 0
                    if ($item['pt']->fresh()->stock === 0) {
                        $item['pt']->update(['activo' => false]);
                    }
                    // Sincronizar el stock total del producto
                    $item['product']->sincronizarStock();
                } else {
                    $item['product']->decrement('stock', $item['quantity']);
                }
            }

            // ── 6. Record payments (optional for pickup orders) ─────────────
            foreach (($data['payments'] ?? []) as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payment['method_id'],
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);
            }

            return $sale;
        });
    }

    /**
     * Advance the estado_pedido through the state machine.
     * Returns true on success, throws Exception on invalid transition.
     *
     * @throws Exception
     */
    public function cambiarEstado(Sale $sale, string $nuevoEstado): Sale
    {
        $transicionesValidas = [
            'pendiente_pago' => ['pagado'],
            'pagado' => ['listo_recojo'],
            'listo_recojo' => ['recogido'],
            'recogido' => [],
        ];

        $estadoActual = $sale->estado_pedido;
        $permitidos = $transicionesValidas[$estadoActual] ?? [];

        if (!in_array($nuevoEstado, $permitidos)) {
            throw new Exception(
                "No se puede pasar de '{$estadoActual}' a '{$nuevoEstado}'."
            );
        }

        $sale->update(['estado_pedido' => $nuevoEstado]);
        return $sale->fresh();
    }

    /**
     * Generate a sequential receipt number (Boleta).
     * Format: B001-000001
     */
    private function generateBoletaNumber(): string
    {
        $lastSale = Sale::whereNotNull('numero_boleta')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastSale || !preg_match('/B001-(\d+)/', $lastSale->numero_boleta, $matches)) {
            return 'B001-000001';
        }

        $nextNumber = intval($matches[1]) + 1;
        return 'B001-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
