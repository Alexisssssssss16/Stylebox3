<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Sales by Day (Last 30 days)
        $salesByDay = Sale::select(DB::raw('DATE(date) as date'), DB::raw('SUM(total) as total'))
            ->whereDate('date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. Sales by Seller
        $salesBySeller = Sale::join('users', 'sales.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(sales.total) as total'), DB::raw('COUNT(sales.id) as count'))
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->get();

        // 3. Inventory Value & Estimated Utility
        $inventoryStats = Product::select(
            DB::raw('SUM(stock * cost) as total_cost'),
            DB::raw('SUM(stock * price) as total_price')
        )->first();

        $estimatedUtility = $inventoryStats->total_price - $inventoryStats->total_cost;

        // 4. Products with Low Rotation (No sales in last 30 days)
        // Logic: Products not present in sale_details where sale date > 30 days ago
        $soldProductIds = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.date', '>=', now()->subDays(30))
            ->pluck('sale_details.product_id');

        $lowRotationProducts = Product::whereNotIn('id', $soldProductIds)
            ->where('stock', '>', 0)
            ->limit(10)
            ->get();

        return view('reports.index', compact('salesByDay', 'salesBySeller', 'inventoryStats', 'estimatedUtility', 'lowRotationProducts'));
    }
}
