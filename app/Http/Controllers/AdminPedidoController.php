<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminPedidoController extends Controller
{
    /**
     * Display a listing of virtual orders.
     */
    public function index(Request $request)
    {
        $query = Sale::where('canal_venta', 'ONLINE')
            ->with(['buyer', 'client'])
            ->latest();

        // Filters
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_boleta', 'like', "%{$search}%")
                    ->orWhereHas('buyer', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('date', $request->fecha);
        }

        $orders = $query->paginate(15);
        $estados = Sale::ESTADOS;

        return view('admin.pedidos.index', compact('orders', 'estados'));
    }

    /**
     * Display the specified virtual order detail.
     */
    public function show(Sale $pedido)
    {
        // Safety check: only online orders in this module
        if ($pedido->canal_venta !== 'ONLINE') {
            return redirect()->route('admin.pedidos.index')
                ->with('error', 'El pedido no es una venta virtual.');
        }

        $pedido->load(['details.product', 'buyer', 'client', 'confirmador', 'payments.paymentMethod']);
        $estados = Sale::ESTADOS;

        return view('admin.pedidos.show', compact('pedido', 'estados'));
    }

    /**
     * Manually confirm payment for a virtual order.
     */
    public function confirmarPago(Sale $pedido)
    {
        try {
            DB::transaction(function () use ($pedido) {
                if ($pedido->estado !== 'pendiente_pago') {
                    throw new Exception("El pedido no está en estado pendiente de pago.");
                }

                $pedido->update([
                    'estado' => 'pagado',
                    'estado_pedido' => 'pagado', // Sync with legacy POS state for consistency
                    'fecha_confirmacion_pago' => now(),
                    'confirmado_por' => Auth::id(),
                ]);
            });

            return back()->with('success', 'Pago confirmado correctamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Change the general order state (Amazon flow).
     */
    public function cambiarEstado(Request $request, Sale $pedido)
    {
        $request->validate([
            'nuevo_estado' => 'required|in:' . implode(',', array_keys(Sale::ESTADOS)),
        ]);

        try {
            $pedido->update(['estado' => $request->nuevo_estado]);

            // Sync legacy state if applicable
            if ($request->nuevo_estado === 'entregado') {
                $pedido->update(['estado_pedido' => 'recogido']);
            }

            return back()->with('success', 'Estado del pedido actualizado a: ' . Sale::ESTADOS[$request->nuevo_estado]);

        } catch (Exception $e) {
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
