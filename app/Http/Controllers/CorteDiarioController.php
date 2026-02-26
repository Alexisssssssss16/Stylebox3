<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorteDiarioController extends Controller
{
    public function generarCorteDiario(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        // Query base para el vendedor actual y el día de hoy
        $salesQuery = Sale::where('user_id', $userId)
            ->whereDate('created_at', $today);

        // 1. Totales Principales
        $totalSales = (float) $salesQuery->where('estado', 'completado')->sum('total');
        $transactionCount = $salesQuery->where('estado', 'completado')->count();
        $voidedSales = $salesQuery->where('estado', 'cancelado')->count();

        // 2. Desglose por Método de Pago
        $paymentBreakdown = DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id')
            ->where('sales.user_id', $userId)
            ->whereDate('sales.created_at', $today)
            ->where('sales.estado', 'completado')
            ->select('payment_methods.name', DB::raw('SUM(sale_payments.amount) as total'))
            ->groupBy('payment_methods.name')
            ->get();

        // 3. Métricas de Performance
        $ticketAverage = $transactionCount > 0 ? ($totalSales / $transactionCount) : 0;

        $firstOrder = Sale::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'asc')
            ->first();

        $lastOrder = Sale::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => now()->format('d/m/Y'),
                'seller' => Auth::user()->name,
                'total_sales' => number_format((float) $totalSales, 2),
                'total_transactions' => $transactionCount,
                'total_income' => number_format((float) $totalSales, 2),
                'voided_sales' => $voidedSales,
                'payment_breakdown' => $paymentBreakdown,
                'ticket_average' => number_format((float) $ticketAverage, 2),
                'first_order_time' => $firstOrder ? $firstOrder->created_at->format('H:i') : 'N/A',
                'last_order_time' => $lastOrder ? $lastOrder->created_at->format('H:i') : 'N/A',
                'recent_sales' => $salesQuery->latest()->take(10)->with('client')->get()->map(fn($s) => [
                    'id' => $s->id,
                    'total' => number_format((float) $s->total, 2),
                    'hour' => $s->created_at->format('H:i'),
                    'client' => $s->client->name ?? 'Venta Rápida',
                    'estado' => $s->estado
                ])
            ]
        ]);
    }
}
