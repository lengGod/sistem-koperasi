<x-app-layout>
    <x-slot name="header">
        <h2>Angsuran</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Angsuran</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Daftar Angsuran</h1>
            <p class="mt-1 text-sm text-outline">Cari, filter, dan kelola data angsuran pinjaman.</p>
        </div>

        <a href="{{ route('installments.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Angsuran
        </a>
    </section>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('installments.index') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nomor pinjaman" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
            <a href="{{ route('installments.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Reset</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">filter_alt</span>
                Terapkan
            </button>
        </div>
    </form>

    @php
        $installmentIds = $installments->pluck('id')->all();
    @endphp

    <form
        x-data="{
            selected: [],
            installmentIds: @js($installmentIds),
            allSelected() { return this.installmentIds.length > 0 && this.selected.length === this.installmentIds.length; },
            toggleAll(checked) { this.selected = checked ? this.installmentIds.map(String) : []; },
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
        action="{{ route('installments.bulk-destroy') }}"
        data-confirm="Anda akan menghapus data angsuran yang dipilih."
        data-confirm-title="Hapus angsuran terpilih"
        data-confirm-message="Tindakan ini akan menghapus semua angsuran yang dicentang dan tidak dapat dibatalkan."
        data-confirm-button="Ya, hapus"
        data-confirm-tone="danger"
        class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest"
    >
        @csrf

        <div class="flex flex-col gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Angsuran</h3>
                <p class="text-sm text-outline">Pilih satu atau lebih angsuran untuk dihapus bersamaan.</p>
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
    <a href="{{ route('installments.index', $memberSortParams) }}" class="inline-flex items-center gap-1 transition hover:text-primary">
        Anggota
        @if ($isMemberNameSort)
            <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
        @endif
    </a>
</th>
                        <th class="px-6 py-4">Pinjaman</th>
                        <th class="px-6 py-4">No.</th>
                        <th class="px-6 py-4">Jatuh Tempo</th>
                        <th class="px-6 py-4">Tagihan</th>
                        <th class="px-6 py-4">Dibayar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($installments as $installment)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="installment_ids[]" value="{{ $installment->id }}" class="rounded border-outline-variant text-primary focus:ring-primary" :checked="selected.includes(String({{ $installment->id }}))" @change="toggleOne({{ $installment->id }}, $event.target.checked)">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $installment->loan?->member?->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $installment->loan?->loan_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $installment->installment_number }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ optional($installment->due_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">Rp {{ number_format((float) $installment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">Rp {{ number_format((float) $installment->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $instStatusLabel = match($installment->status) {
                                        'pending' => 'Menunggu',
                                        'partial' => 'Sebagian',
                                        'paid'    => 'Lunas',
                                        'late'    => 'Terlambat',
                                        default   => ucfirst($installment->status),
                                    };
                                    $instStatusClass = match($installment->status) {
                                        'paid'    => 'bg-emerald-100 text-emerald-800',
                                        'partial' => 'bg-blue-100 text-blue-800',
                                        'late'    => 'bg-red-100 text-red-800',
                                        default   => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-extrabold {{ $instStatusClass }}">
                                    {{ $instStatusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('installments.show', $installment) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Detail</a>
                                    <a href="{{ route('installments.edit', $installment) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-6 py-10 text-center text-sm text-outline">Belum ada data angsuran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">{{ $installments->links() }}</div>
    </form>
</x-app-layout>
