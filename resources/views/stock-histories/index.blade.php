<x-app-layout>
    <x-slot name="header">
        <h2>Riwayat Stok</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Audit</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Riwayat Perubahan Stok</h1>
            <p class="mt-1 text-sm text-outline">Data historis perubahan stok barang (masuk, keluar, penyesuaian).</p>
        </div>
    </section>

    <form method="GET" action="{{ route('stock-histories.index') }}"
        class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label for="type" class="mb-2 block text-sm font-bold text-on-surface">Jenis Transaksi</label>
                <select id="type" name="type"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    <option value="masuk" @selected(request('type') === 'masuk')>Masuk</option>
                    <option value="keluar" @selected(request('type') === 'keluar')>Keluar</option>
                    <option value="penyesuaian" @selected(request('type') === 'penyesuaian')>Penyesuaian</option>
                </select>
            </div>
            <div>
                <label for="start_date" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="end_date" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap justify-end gap-2">
            <a href="{{ route('stock-histories.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                Reset
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">filter_alt</span>
                Terapkan Filter
            </button>
        </div>
    </form>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Kode Barang</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4 text-center">Jml</th>
                        <th class="px-6 py-4 text-center">Stok Sblm</th>
                        <th class="px-6 py-4 text-center">Stok Ssdh</th>
                        <th class="px-6 py-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($histories as $history)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $history->id }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $history->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $history->product->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $history->product->sku }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize px-2 py-1 rounded-full text-xs font-bold
                                    {{ $history->type === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 
                                       ($history->type === 'keluar' ? 'bg-error-container text-on-error-container' : 'bg-surface-container-high text-on-surface') }}">
                                    {{ $history->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold {{ $history->quantity_change > 0 ? 'text-emerald-700' : 'text-error' }}">
                                {{ $history->quantity_change > 0 ? '+' : '' }}{{ $history->quantity_change }}
                            </td>
                            <td class="px-6 py-4 text-center text-on-surface-variant">{{ $history->stock_before }}</td>
                            <td class="px-6 py-4 text-center font-bold text-on-surface">{{ $history->stock_after }}</td>
                            <td class="px-6 py-4 text-on-surface-variant text-xs">{{ $history->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-outline">
                                Tidak ada riwayat transaksi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">
            {{ $histories->links() }}
        </div>
    </div>
</x-app-layout>
