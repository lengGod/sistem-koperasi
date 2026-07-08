<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockHistory;
use Illuminate\View\View;

class PetugasDashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalTransactionsToday = Transaction::whereDate('created_at', today())->count();
        $totalRevenueToday = Transaction::whereDate('created_at', today())->sum('total_price');
        $lowStockProducts = Product::where('stock', '<=', 10)->limit(5)->get();
        $recentTransactions = Transaction::with('items')->latest()->limit(5)->get();

        return view('dashboard-petugas', compact(
            'totalProducts',
            'totalTransactionsToday',
            'totalRevenueToday',
            'lowStockProducts',
            'recentTransactions'
        ));
    }
}
