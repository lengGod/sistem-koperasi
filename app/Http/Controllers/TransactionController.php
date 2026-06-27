<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function index()
    {
        Gate::authorize('view_stock');
        $transactions = Transaction::with('items.product')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('perform_transaction');

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalPrice = 0;
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'total_price' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $product->decrement('stock', $item['quantity']);
                
                $price = $product->price * $item['quantity'];
                $totalPrice += $price;

                $transaction->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            $transaction->update(['total_price' => $totalPrice]);

            return redirect()->route('transactions.index')->with('success', 'Transaction completed successfully.');
        });
    }
}
