<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        Gate::authorize('view_stock');
        $transactions = Transaction::with('items.product')->latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        Gate::authorize('perform_transaction');
        $products = Product::where('stock', '>', 0)->get();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        Gate::authorize('perform_transaction');

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $totalPrice = 0;
            // Generate custom ID (e.g., TRX-20260629-XXXX)
            $customId = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $transaction = Transaction::create([
                'custom_id' => $customId,
                'user_id' => auth()->id(),
                'total_price' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Menggunakan InventoryService untuk mengurangi stok dan mencatat riwayat
                $this->inventoryService->adjustStock(
                    $product->id,
                    -$item['quantity'],
                    'keluar',
                    'Transaksi Penjualan #' . $customId
                );
                
                $price = $product->price * $item['quantity'];
                $totalPrice += $price;

                $transaction->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            $transaction->update(['total_price' => $totalPrice]);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diproses.');
    }

    public function reverse(Transaction $transaction)
    {
        Gate::authorize('perform_transaction');

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                // Kembalikan stok (inverse dari transaksi keluar)
                $this->inventoryService->adjustStock(
                    $item->product_id,
                    $item->quantity,
                    'penyesuaian',
                    'Pembatalan Transaksi #' . $transaction->custom_id
                );
            }

            // Hapus items lalu transaksi
            $transaction->items()->delete();
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
    }

    public function bulkReverse(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('perform_transaction');

        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['integer', 'distinct', 'exists:transactions,id'],
        ]);

        $ids = $validated['transaction_ids'];

        DB::transaction(function () use ($ids) {
            $transactions = Transaction::whereIn('id', $ids)->with('items')->get();

            foreach ($transactions as $transaction) {
                foreach ($transaction->items as $item) {
                    // Kembalikan stok (inverse dari transaksi keluar)
                    $this->inventoryService->adjustStock(
                        $item->product_id,
                        $item->quantity,
                        'penyesuaian',
                        'Pembatalan Transaksi #' . $transaction->custom_id
                    );
                }

                // Hapus items lalu transaksi
                $transaction->items()->delete();
                $transaction->delete();
            }
        });

        return redirect()->route('transactions.index')->with('status', 'Transaksi yang dipilih berhasil dibatalkan dan stok dikembalikan.');
    }
}
