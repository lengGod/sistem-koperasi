<?php

namespace App\Http\Controllers;

use App\Models\ParkingTransaction;
use Illuminate\Http\Request;

class ParkingTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = ParkingTransaction::with('vehicleType');

        if ($request->filled('search')) {
            $query->where('license_plate', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(20);
        return view('parking-transactions.index', compact('transactions'));
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['integer', 'distinct', 'exists:parking_transactions,id'],
        ]);

        ParkingTransaction::whereIn('id', $validated['transaction_ids'])->delete();

        return redirect()->route('parking-transactions.index')->with('success', 'Transaksi terpilih berhasil dihapus.');
    }
}
