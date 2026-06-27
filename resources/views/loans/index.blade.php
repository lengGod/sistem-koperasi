<x-app-layout>
    <x-slot name="header">
        <h2>Pinjaman</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Pinjaman</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Daftar Pinjaman</h1>
            <p class="mt-1 text-sm text-outline">Cari, filter, dan kelola data pinjaman anggota.</p>
        </div>

        <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Pinjaman
        </a>
    </section>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('loans.index') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nama atau no rekening" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draf</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="completed" @selected(request('status') === 'completed')>Lunas</option>
                    <option value="overdue" @selected(request('status') === 'overdue')>Terlambat</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Reset</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">filter_alt</span>
                Terapkan
            </button>
        </div>
    </form>

    @php
        $loanIds = $loans->pluck('id')->all();
    @endphp

    <form
        x-data="{
            selected: [],
            loanIds: @js($loanIds),
            allSelected() { return this.loanIds.length > 0 && this.selected.length === this.loanIds.length; },
            toggleAll(checked) { this.selected = checked ? this.loanIds.map(String) : []; },
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
        action="{{ route('loans.bulk-destroy') }}"
        data-confirm="Anda akan menghapus data pinjaman yang dipilih."
        data-confirm-title="Hapus pinjaman terpilih"
        data-confirm-message="Tindakan ini akan menghapus semua pinjaman yang dicentang beserta data terkait dan tidak dapat dibatalkan."
        data-confirm-button="Ya, hapus"
        data-confirm-tone="danger"
        class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest"
    >
        @csrf

        <div class="flex flex-col gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Pinjaman</h3>
                <p class="text-sm text-outline">Pilih satu atau lebih pinjaman untuk dihapus bersamaan.</p>
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
                            <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" @click="toggleAll(!allSelected())" :checked="allSelected()">
                        </th>
                        @php
                            $isMemberNameSort = request('sort') === 'member_name';
                            $memberSortParams = request()->except('sort');
                            if (! $isMemberNameSort) {
                                $memberSortParams['sort'] = 'member_name';
                            }
                        @endphp
                        <th class="px-6 py-4">
                            <a href="{{ route('loans.index', $memberSortParams) }}" class="inline-flex items-center gap-1 transition hover:text-primary">
                                Nama
                                @if ($isMemberNameSort)
                                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4">No Pinjaman</th>
                        <th class="px-6 py-4">Plafond</th>
                        <th class="px-6 py-4">Sisa Pinjaman</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($loans as $loan)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="loan_ids[]" value="{{ $loan->id }}" class="rounded border-outline-variant text-primary focus:ring-primary" :checked="selected.includes(String({{ $loan->id }}))" @change="toggleOne({{ $loan->id }}, $event.target.checked)">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $loan->member?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $loan->loan_number }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">Rp {{ number_format((float) $loan->principal_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-bold text-amber-700">Rp {{ number_format((float) $loan->remaining_balance, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-extrabold bg-surface-container-high">
                                    {{ $loan->status === 'active' ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('loans.show', $loan) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Detail</a>
                                    <a href="{{ route('loans.edit', $loan) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-outline">Belum ada data pinjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">{{ $loans->links() }}</div>
    </form>
</x-app-layout>
