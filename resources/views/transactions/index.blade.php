<x-app-layout>
    <x-slot name="header">
        <h2>Riwayat Transaksi</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Transaksi</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Riwayat Transaksi</h1>
            <p class="mt-1 text-sm text-outline">Kelola data transaksi kasir koperasi.</p>
        </div>

        <a href="{{ route('transactions.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Buat Transaksi Baru
        </a>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest" x-data="{ selected: [] }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <h2 class="font-bold text-on-surface">Riwayat Transaksi</h2>
            <form action="{{ route('transactions.bulk-reverse') }}" method="POST"
                x-show="selected.length > 0"
                x-transition
                data-confirm="Apakah Anda yakin ingin membatalkan transaksi-transaksi yang dipilih? Stok akan dikembalikan."
                data-confirm-title="Batalkan Transaksi" data-confirm-button="Ya, Batalkan"
                data-confirm-tone="danger">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="transaction_ids[]" :value="id">
                </template>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="selected.length === 0">
                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Batalkan Terpilih
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" @change="selected = $event.target.checked ? {{ $transactions->pluck('id') }} : []"
                                class="rounded border-outline text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-4">ID Transaksi</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4 text-right">Total Harga</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" value="{{ $transaction->id }}" x-model="selected"
                                    class="rounded border-outline text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 font-bold text-primary">{{ $transaction->custom_id }}</td>
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
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('transactions.reverse', $transaction->id) }}" method="POST" 
                                    data-confirm="Apakah Anda yakin ingin membatalkan transaksi ini? Stok akan dikembalikan."
                                    data-confirm-title="Batalkan Transaksi"
                                    data-confirm-button="Ya, Batalkan"
                                    data-confirm-tone="danger">
                                    @csrf
                                    <button type="submit" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-error transition hover:bg-error-container">
                                        Batalkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
