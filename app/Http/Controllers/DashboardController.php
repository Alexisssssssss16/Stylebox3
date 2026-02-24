<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Redirect logic if someone hits /dashboard directly (optional backup)
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminIndex();
        } elseif ($user->hasRole('vendedor')) {
            return $this->sellerIndex();
        } else {
            // Default for buyers and others
            return redirect()->route('historial.index');
        }
    }

    public function buyerIndex()
    {
        return redirect()->route('historial.index');
    }

    public function adminIndex()
    {
        return view('dashboard.admin');
    }

    public function sellerIndex()
    {
        return view('dashboard.seller');
    }
}
