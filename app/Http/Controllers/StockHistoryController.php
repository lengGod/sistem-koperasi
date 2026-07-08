<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockHistoryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_stock');

        $query = StockHistory::with('product')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $histories = $query->paginate(20)->withQueryString();

        return view('stock-histories.index', compact('histories'));
    }
}
