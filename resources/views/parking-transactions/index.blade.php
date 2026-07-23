<x-app-layout>
    <x-slot name="header">
        <h2>Transaksi Parkir</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Parkir</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Transaksi Parkir</h1>
            <p class="mt-1 text-sm text-outline">Kelola data kendaraan masuk dan keluar.</p>
        </div>

        <a href="#" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Catat Kendaraan Masuk
        </a>
    </section>

    <form method="GET" action="{{ route('parking-transactions.index') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari Plat Nomor</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Contoh: B 1234 ABC"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <a href="{{ route('parking-transactions.index') }}" class="rounded-xl border border-outline-variant px-4 py-2.5 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Reset</a>
                <button type="submit" class="flex-1 rounded-xl bg-primary-container px-4 py-2.5 text-sm font-bold text-on-primary transition hover:opacity-90">Filter</button>
            </div>
        </div>
    </form>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest" x-data="{ selected: [] }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <h2 class="font-bold text-on-surface">Daftar Transaksi</h2>
            <form action="{{ route('parking-transactions.bulk-destroy') }}" method="POST"
                x-show="selected.length > 0"
                x-transition
                data-confirm="Anda yakin ingin menghapus transaksi yang dipilih? Tindakan ini tidak dapat dibatalkan."
                data-confirm-title="Hapus transaksi masal" data-confirm-button="Ya, hapus"
                data-confirm-tone="danger">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="transaction_ids[]" :value="id">
                </template>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="selected.length === 0">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Hapus Terpilih
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
                        <th class="px-6 py-4">Plat Nomor</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Masuk</th>
                        <th class="px-6 py-4">Keluar</th>
                        <th class="px-6 py-4 text-right">Total Biaya</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($transactions as $tx)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" value="{{ $tx->id }}" x-model="selected"
                                    class="rounded border-outline text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $tx->license_plate }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $tx->vehicleType->name }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $tx->entry_time->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $tx->exit_time ? $tx->exit_time->format('d M Y H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-on-surface">
                                {{ $tx->total_fee ? 'Rp ' . number_format($tx->total_fee, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full px-2 py-1 text-xs font-bold {{ $tx->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada transaksi parkir.
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
