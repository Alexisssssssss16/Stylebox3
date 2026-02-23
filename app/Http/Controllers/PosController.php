<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;

class PosController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index()
    {
        $products = Product::active()->get();
        $clients = Client::where('status', true)->get();
        $paymentMethods = PaymentMethod::where('status', true)->get();

        return view('pos.index', compact('products', 'clients', 'paymentMethods'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = $this->saleService->createSale($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'sale_id' => $sale->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}

