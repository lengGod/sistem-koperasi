<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update stock and log the history atomically.
     *
     * @param int $productId
     * @param int $quantityChange (positive for incoming, negative for outgoing/adjustment)
     * @param string $type ('masuk', 'keluar', 'penyesuaian')
     * @param string|null $description
     * @return void
     * @throws \Exception
     */
    public function adjustStock(int $productId, int $quantityChange, string $type, ?string $description = null): void
    {
        DB::transaction(function () use ($productId, $quantityChange, $type, $description) {
            $product = Product::findOrFail($productId);
            
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore + $quantityChange;

            if ($stockAfter < 0) {
                throw new \Exception("Stok tidak boleh bernilai negatif. Stok saat ini: {$stockBefore}, Perubahan: {$quantityChange}");
            }

            $product->update(['stock' => $stockAfter]);

            StockHistory::create([
                'product_id' => $productId,
                'type' => $type,
                'quantity_change' => $quantityChange,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }
}
