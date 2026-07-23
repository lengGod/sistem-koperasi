<x-app-layout>
    <x-slot name="header">
        <h2>Laporan Keuntungan</h2>
    </x-slot>

    <div class="mb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Laporan</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Keuntungan Penjualan</h1>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('reports.profit') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari Barang</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..."
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="start_month" class="mb-2 block text-sm font-bold text-on-surface">Dari Bulan</label>
                <input id="start_month" name="start_month" type="month" value="{{ request('start_month') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="end_month" class="mb-2 block text-sm font-bold text-on-surface">Sampai Bulan</label>
                <input id="end_month" name="end_month" type="month" value="{{ request('end_month') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-xl bg-primary-container px-4 py-2.5 text-sm font-bold text-on-primary">Filter Laporan</button>
                <a href="{{ route('reports.profit.export', request()->query()) }}" data-turbo="false" class="w-full rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-on-secondary-container text-center">Export</a>
            </div>
        </div>
    </form>

    @php
        $totalModal = 0;
        $totalJual = 0;
        $totalUntung = 0;
        foreach ($products as $p) {
            $sold = $p->total_sold ?? 0;
            $purchased = $p->total_purchased ?? 0;
            $totalModal += $p->purchase_price * $purchased;
            $totalJual += $p->price * $sold;
            $totalUntung += ($p->price - $p->purchase_price) * $sold;
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <p class="text-sm text-outline">Total Modal</p>
            <p class="text-2xl font-bold text-on-surface">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
        </div>
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <p class="text-sm text-outline">Total Penjualan</p>
            <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalJual, 0, ',', '.') }}</p>
        </div>
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <p class="text-sm text-outline">Total Keuntungan</p>
            <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalUntung, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4 text-center">Terjual / Stok</th>
                        <th class="px-6 py-4 text-right">Modal (Satuan)</th>
                        <th class="px-6 py-4 text-right">Jual (Satuan)</th>
                        <th class="px-6 py-4 text-right">Total Jual</th>
                        <th class="px-6 py-4 text-right">Untung (Satuan)</th>
                        <th class="px-6 py-4 text-right">Total Untung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach ($products as $product)
                        @php
                            $sold = $product->total_sold ?? 0;
                            $profitPerUnit = $product->price - $product->purchase_price;
                            $totalJualItem = $product->price * $sold;
                            $totalProfit = $profitPerUnit * $sold;
                        @endphp
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-center">{{ $sold }} / {{ $product->stock }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">Rp {{ number_format($totalJualItem, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold {{ $profitPerUnit < 0 ? 'text-error' : 'text-on-surface' }}">
                                Rp {{ number_format($profitPerUnit, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-on-surface">
                                Rp {{ number_format($totalProfit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
