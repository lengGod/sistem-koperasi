<x-app-layout>
    <x-slot name="header">
        <h2>Riwayat Transaksi</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Modul Kasir</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Riwayat Transaksi</h1>
            <p class="mt-1 text-sm text-outline">Daftar semua transaksi penjualan yang telah dilakukan.</p>
        </div>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">ID Transaksi</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4 text-right">Total Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-bold text-primary">#{{ $transaction->id }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                @foreach ($transaction->items as $item)
                                    <div class="text-xs text-on-surface-variant">
                                        • {{ $item->product->name }} ({{ $item->quantity }}x)
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-on-surface">
                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
