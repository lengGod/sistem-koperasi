<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_stock');

        $month = $request->input('month'); // Format YYYY-MM
        
        $query = Product::query();

        if ($month) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$month]);
        }

        // Total Terjual (items) berdasarkan periode
        $query->withSum(['items as total_sold' => function ($query) use ($month) {
            if ($month) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month]);
            }
        }], 'quantity');

        // Total Pembelian (stockHistories) berdasarkan periode
        $query->withSum(['stockHistories as total_purchased' => function ($query) use ($month) {
            $query->where('type', 'masuk');
            if ($month) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month]);
            }
        }], 'quantity_change');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return view('reports.profit', compact('products', 'month'));
    }
}
