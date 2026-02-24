<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ProductoTalla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obtiene las notificaciones dinámicas para el usuario actual.
     */
    public function getNotifications()
    {
        $notifications = [];

        // 1. Alertas de Stock Bajo (Menos de 6 unidades por variante)
        $lowStockItems = ProductoTalla::with(['producto', 'talla'])
            ->where('stock', '<', 6)
            ->where('activo', true)
            ->get();

        foreach ($lowStockItems as $item) {
            $notifications[] = [
                'type' => 'stock',
                'title' => 'Stock Bajo: ' . $item->producto->name,
                'message' => 'Quedan ' . $item->stock . ' unidades en talla ' . $item->talla->nombre,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'text-warning',
                'bg' => 'bg-warning-subtle',
                'time' => 'Inventario',
                'url' => route('inventario.tallas', ['producto' => $item->producto->name])
            ];
        }

        // 2. Nuevos Pedidos Virtuales (en las últimas 24 horas)
        // Solo para admin o vendedores que gestionan ventas
        if (Auth::user()->hasRole(['admin', 'vendedor'])) {
            $recentSales = Sale::where('created_at', '>=', now()->subHours(24))
                ->latest()
                ->take(5)
                ->get();

            foreach ($recentSales as $sale) {
                $notifications[] = [
                    'type' => 'sale',
                    'title' => 'Venta Realizada #' . $sale->id,
                    'message' => 'Monto total: S/ ' . number_format($sale->total, 2),
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'text-success',
                    'bg' => 'bg-success-subtle',
                    'time' => $sale->created_at->diffForHumans(),
                    'url' => '#'
                ];
            }
        }

        return response()->json([
            'count' => count($notifications),
            'notifications' => array_slice($notifications, 0, 10) // Máximo 10 notificaciones
        ]);
    }
}
