<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard Petugas</h2>
    </x-slot>

    <section class="mb-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Ringkasan Operasional</p>
            <h2 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Dashboard Petugas</h2>
            <p class="mt-1 text-sm text-outline">Pantau stok barang, transaksi kasir, dan ringkasan pendapatan hari ini.</p>
        </div>
    </section>

    <section class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['label' => 'Total Produk', 'value' => number_format($totalProducts), 'icon' => 'inventory_2', 'tone' => 'bg-primary-fixed text-primary'],
            ['label' => 'Transaksi Hari Ini', 'value' => number_format($totalTransactionsToday), 'icon' => 'point_of_sale', 'tone' => 'bg-secondary-fixed text-secondary'],
            ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp ' . number_format($totalRevenueToday, 0, ',', '.'), 'icon' => 'trending_up', 'tone' => 'bg-tertiary-fixed text-tertiary']
        ] as $card)
            <article class="dashboard-card rounded-3xl border border-white/70 bg-surface-container-lowest p-6">
                <div class="mb-5 flex items-start justify-between">
                    <div class="{{ $card['tone'] }} flex h-12 w-12 items-center justify-center rounded-2xl">
                        <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
                    </div>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">{{ $card['label'] }}</p>
                <h3 class="mt-1 text-3xl font-extrabold tracking-tight text-on-surface">{{ $card['value'] }}</h3>
            </article>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-on-surface">Stok Menipis</h3>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-4">
                @forelse ($lowStockProducts as $product)
                    <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
                        <span class="font-semibold text-on-surface">{{ $product->name }}</span>
                        <span class="rounded-full bg-error-container px-3 py-1 text-xs font-bold text-on-error-container">Stok: {{ $product->stock }}</span>
                    </div>
                @empty
                    <p class="text-sm text-outline">Tidak ada produk dengan stok menipis.</p>
                @endforelse
            </div>
        </article>

        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-on-surface">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-4">
                @forelse ($recentTransactions as $transaction)
                    <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
                        <div>
                            <p class="text-sm font-semibold text-on-surface">ID Transaksi: {{ $transaction->id }}</p>
                            <p class="text-xs text-outline">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="font-bold text-primary">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-outline">Belum ada transaksi.</p>
                @endforelse
            </div>
        </article>
    </section>
</x-app-layout>
