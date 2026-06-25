<x-app-layout>
    <x-slot name="title">Daftar Simpanan</x-slot>
    <x-slot name="header">
        <h2>Simpanan</h2>
    </x-slot>
    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Simpanan</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Daftar Simpanan</h1>
            <p class="mt-1 text-sm text-outline">Cari, filter, dan kelola data simpanan koperasi.</p>
        </div>

        <a href="{{ route('savings.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Simpanan
        </a>
    </section>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('savings.index') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nomor referensi atau nama anggota" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="type" class="mb-2 block text-sm font-bold text-on-surface">Tipe</label>
                <select id="type" name="type" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    <option value="deposit" @selected(request('type') === 'deposit')>Setoran</option>
                    <option value="withdrawal" @selected(request('type') === 'withdrawal')>Penarikan</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
            <a href="{{ route('savings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Reset</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">filter_alt</span>
                Terapkan
            </button>
        </div>
    </form>

    @php
        $savingsIds = $savings->pluck('id')->all();
    @endphp

    <form
        x-data="{
            selected: [],
            savingsIds: @js($savingsIds),
            allSelected() { return this.savingsIds.length > 0 && this.selected.length === this.savingsIds.length; },
            toggleAll(checked) { this.selected = checked ? this.savingsIds.slice() : []; },
            toggleOne(id, checked) {
                const stringId = String(id);
                if (checked) {
                    if (!this.selected.includes(stringId)) this.selected.push(stringId);
                } else {
                    this.selected = this.selected.filter((value) => value !== stringId);
                }
            },
        }"
        method="POST"
        action="{{ route('savings.bulk-destroy') }}"
        data-confirm="Anda akan menghapus transaksi simpanan yang dipilih."
        data-confirm-title="Hapus simpanan terpilih"
        data-confirm-message="Tindakan ini akan menghapus semua transaksi simpanan yang dicentang dan tidak dapat dibatalkan."
        data-confirm-button="Ya, hapus"
        data-confirm-tone="danger"
        class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest"
    >
        @csrf

        <div class="flex flex-col gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Simpanan</h3>
                <p class="text-sm text-outline">Pilih satu atau lebih transaksi simpanan untuk dihapus bersamaan.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low" @click="toggleAll(!allSelected())">
                    Pilih Semua
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50" :disabled="selected.length === 0">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="w-14 px-6 py-4">
                            <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" :checked="allSelected()" @change="toggleAll($event.target.checked)">
                        </th>
                        <th class="px-6 py-4">Referensi</th>
                        <th class="px-6 py-4">Anggota</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($savings as $saving)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="savings_ids[]" value="{{ $saving->id }}" class="rounded border-outline-variant text-primary focus:ring-primary" :checked="selected.includes(String({{ $saving->id }}))" @change="toggleOne({{ $saving->id }}, $event.target.checked)">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $saving->reference_number ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $saving->member?->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $saving->savingsType?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="{{ $saving->transaction_type === 'deposit' ? 'bg-emerald-100 text-emerald-800' : 'bg-error-container text-on-error-container' }} rounded-full px-2.5 py-1 text-xs font-extrabold">
                                    {{ $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">Rp {{ number_format((float) $saving->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ optional($saving->transaction_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('savings.show', $saving) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Detail</a>
                                    <a href="{{ route('savings.edit', $saving) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-outline">Belum ada transaksi simpanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">{{ $savings->links() }}</div>
    </form>
</x-app-layout>
