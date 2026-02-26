<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardStatsController extends Controller
{
    public function getAdminStats(Request $request)
    {
        $range = $request->query('range', '7d');
        return response()->json($this->prepareAdminStatsPayload($range));
    }

    /**
     * Prepara el payload completo de estadísticas para Admin.
     * Reutilizado para carga inicial y eventos de broadcasting.
     */
    public function prepareAdminStatsPayload($range = '7d')
    {
        $days = match ($range) {
            '24h' => 1,
            '30d' => 30,
            '6m' => 180,
            default => 7,
        };

        $today = now();
        $startOfDay = $today->copy()->startOfDay();
        $endOfDay = $today->copy()->endOfDay();
        $startDate = $today->copy()->subDays($days)->startOfDay();

        // 1. Totals
        $totalSales = (float) (Sale::sum('total') ?? 0);
        $totalSalesToday = (float) (Sale::whereBetween('created_at', [$startOfDay, $endOfDay])->sum('total') ?? 0);
        $transactionCount = Sale::where('created_at', '>=', $startDate)->count();
        $productsLowStock = Product::where('stock', '<', 5)->count();
        $totalUsers = User::count();

        // 2. Best Selling Products (Top 5)
        $topProducts = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_qty'), DB::raw('SUM(sale_details.subtotal) as total_revenue'))
            ->groupBy('products.name', 'products.id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $bestSeller = $topProducts->first();

        // 3. Performance Chart
        $salesChart = Sale::select(DB::raw('DATE(created_at) as sale_date'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date')
            ->get();

        // 4. Sparkline Data (Simple variation trend)
        $sparklineSales = Sale::select(DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total');

        // 5. Recent Activity (Merged Sales and User Registrations)
        $recentSales = Sale::latest()->take(5)->get()->map(fn($s) => [
            'type' => 'sale',
            'title' => 'Nueva Venta',
            'subtitle' => 'S/ ' . number_format((float) ($s->total ?? 0), 2),
            'time' => $s->created_at->diffForHumans()
        ]);

        $recentUsers = User::latest()->take(3)->get()->map(fn($u) => [
            'type' => 'user',
            'title' => 'Nuevo Usuario',
            'subtitle' => $u->name,
            'time' => $u->created_at->diffForHumans()
        ]);

        $activity = $recentSales->concat($recentUsers)->sortByDesc('time')->take(6)->values();

        // 6. Important Alerts
        $alerts = [];
        if ($productsLowStock > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Hay {$productsLowStock} productos con stock crítico.",
                'action' => route('products.index')
            ];
        }

        $failedPayments = Sale::where('estado', 'cancelado')->where('created_at', '>=', now()->subDay())->count();
        if ($failedPayments > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Se detectaron {$failedPayments} transacciones fallidas hoy.",
                'action' => '#'
            ];
        }

        return [
            'totalSales' => number_format($totalSales, 2),
            'totalSalesToday' => number_format($totalSalesToday, 2),
            'transactionCount' => number_format($transactionCount),
            'productsLowStock' => $productsLowStock,
            'totalUsers' => number_format($totalUsers),
            'bestSeller' => $bestSeller ? $bestSeller->name : 'N/A',
            'topProducts' => $topProducts,
            'chartLabels' => $salesChart->pluck('sale_date'),
            'chartData' => $salesChart->pluck('total'),
            'sparklines' => [
                'sales' => $sparklineSales,
                'transactions' => Sale::where('created_at', '>=', now()->subDays(7))->groupBy(DB::raw('DATE(created_at)'))->select(DB::raw('COUNT(*) as total'))->pluck('total')
            ],
            'recentActivity' => $activity,
            'alerts' => $alerts
        ];
    }

    public function getSellerStats()
    {
        $userId = Auth::id();
        $now = now();
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfWeek = $now->copy()->subDays(7)->startOfDay();

        // 1. Core Totals (Today)
        $mySalesToday = (float) (Sale::where('user_id', $userId)->whereBetween('created_at', [$startOfDay, $endOfDay])->sum('total') ?? 0);
        $myTransactionCount = Sale::where('user_id', $userId)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();

        // 2. monthly & Averages
        $mySalesMonth = (float) (Sale::where('user_id', $userId)->where('created_at', '>=', $startOfMonth)->sum('total') ?? 0);
        $ticketAverage = (float) ($myTransactionCount > 0 ? (float) $mySalesToday / $myTransactionCount : 0);

        // 3. New Clients Today (Clients registered today by this seller)
        $newClientsToday = Client::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        // 4. Daily Trend (Last 7 Days)
        $dailyTrend = Sale::where('user_id', $userId)
            ->where('created_at', '>=', $startOfWeek)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $trendLabels = $dailyTrend->pluck('date')->toArray();
        $trendData = $dailyTrend->pluck('total')->map(fn($v) => (float) $v)->toArray();

        // 5. Payment Methods (Real data from payments table)
        // Adjusting based on standard SAF structure (SalePayment links Sale to PaymentMethod)
        $paymentBreakdown = DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id')
            ->where('sales.user_id', $userId)
            ->whereBetween('sales.created_at', [$startOfDay, $endOfDay])
            ->select('payment_methods.name', DB::raw('SUM(sale_payments.amount) as total'))
            ->groupBy('payment_methods.name')
            ->get();

        $payLabels = $paymentBreakdown->pluck('name')->toArray();
        $payData = $paymentBreakdown->pluck('total')->map(fn($v) => (float) $v)->toArray();

        // 6. Top 5 Products (Specifically for this seller)
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->where('sales.user_id', $userId)
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_qty'))
            ->groupBy('products.name', 'products.id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 7. Recent Sales with Detail
        $recentSales = Sale::where('user_id', $userId)
            ->with(['client:id,name', 'details.product'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'client' => $sale->client ? $sale->client->name : 'General',
                    'total' => number_format((float) $sale->total, 2),
                    'estado' => $sale->estado,
                    'time' => $sale->created_at->diffForHumans(),
                    'hora' => $sale->created_at->format('H:i'),
                    'method' => $sale->payments->first()?->paymentMethod->name ?? 'N/A'
                ];
            });

        return response()->json([
            'mySalesToday' => number_format($mySalesToday, 2),
            'mySalesMonth' => number_format($mySalesMonth, 2),
            'myTransactionCount' => $myTransactionCount,
            'ticketAverage' => number_format($ticketAverage, 2),
            'newClientsToday' => $newClientsToday,
            'dailyTrend' => [
                'labels' => $trendLabels,
                'data' => $trendData
            ],
            'paymentStats' => [
                'labels' => $payLabels,
                'data' => $payData
            ],
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'lowStockAlerts' => Product::where('stock', '<', 5)->count()
        ]);
    }

    public function getCorteDiarioData()
    {
        $userId = Auth::id();
        $today = now();
        $startOfDay = $today->copy()->startOfDay();
        $endOfDay = $today->copy()->endOfDay();

        $sales = Sale::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['payments.paymentMethod', 'client'])
            ->get();

        $totalIncome = $sales->sum('total');
        $transactionCount = $sales->count();

        $paymentBreakdown = DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id')
            ->where('sales.user_id', $userId)
            ->whereBetween('sales.created_at', [$startOfDay, $endOfDay])
            ->select('payment_methods.name', DB::raw('SUM(sale_payments.amount) as total'))
            ->groupBy('payment_methods.name')
            ->get();

        return response()->json([
            'success' => true,
            'date' => $today->format('d/m/Y'),
            'seller' => Auth::user()->name,
            'totalIncome' => number_format((float) $totalIncome, 2),
            'transactionCount' => $transactionCount,
            'paymentBreakdown' => $paymentBreakdown,
            'recentSales' => $sales->take(10)->map(fn($s) => [
                'id' => $s->id,
                'total' => number_format((float) $s->total, 2),
                'hour' => $s->created_at->format('H:i'),
                'client' => $s->client->name ?? 'General'
            ])
        ]);
    }

    public function getBuyerStats()
    {
        $user = Auth::user();
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json([
                'totalPurchases' => 0,
                'totalSpent' => '0.00',
                'lastOrderDate' => 'N/A',
            ]);
        }

        $totalPurchases = Sale::where('client_id', $client->id)->count();
        $totalSpent = Sale::where('client_id', $client->id)->sum('total');
        $lastOrder = Sale::where('client_id', $client->id)->latest()->first();

        return response()->json([
            'totalPurchases' => $totalPurchases,
            'totalSpent' => number_format($totalSpent, 2),
            'lastOrderDate' => $lastOrder ? $lastOrder->created_at->format('d/m/Y') : 'N/A',
        ]);
    }
}
