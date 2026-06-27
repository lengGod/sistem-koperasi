<x-app-layout>
    <x-slot name="header">
        <h2>Jenis Simpanan</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Simpanan</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Jenis Simpanan</h1>
            <p class="mt-1 text-sm text-outline">Kelola kategori jenis simpanan koperasi.</p>
        </div>

        <a href="{{ route('savings-types.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Jenis
        </a>
    </section>

    @php
        $savingsTypeIds = $savingsTypes->pluck('id')->all();
    @endphp

    <form
        x-data="{
            selected: [],
            savingsTypeIds: @js($savingsTypeIds),
            allSelected() { return this.savingsTypeIds.length > 0 && this.selected.length === this.savingsTypeIds.length; },
            toggleAll(checked) { this.selected = checked ? this.savingsTypeIds.map(String) : []; },
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
        action="{{ route('savings-types.bulk-destroy') }}"
        data-confirm="Anda akan menghapus jenis simpanan yang dipilih."
        data-confirm-title="Hapus jenis simpanan terpilih"
        data-confirm-message="Tindakan ini akan menghapus semua jenis simpanan yang dicentang beserta data transaksi terkait dan tidak dapat dibatalkan."
        data-confirm-button="Ya, hapus"
        data-confirm-tone="danger"
        class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest"
    >
        @csrf

        <div class="flex flex-col gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Jenis Simpanan</h3>
                <p class="text-sm text-outline">Pilih satu atau lebih jenis simpanan untuk dihapus bersamaan.</p>
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
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="w-14 px-6 py-4">
                            <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" @click="toggleAll(!allSelected())" :checked="allSelected()">
                        </th>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Wajib</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($savingsTypes as $savingsType)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="savings_type_ids[]" value="{{ $savingsType->id }}" class="rounded border-outline-variant text-primary focus:ring-primary" :checked="selected.includes(String({{ $savingsType->id }}))" @change="toggleOne({{ $savingsType->id }}, $event.target.checked)">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $savingsType->code }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-on-surface">{{ $savingsType->name }}</div>
                                <div class="text-xs text-outline">{{ $savingsType->description ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $savingsType->is_mandatory ? 'Ya' : 'Tidak' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-extrabold
                                    {{ $savingsType->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-error-container text-on-error-container' }}">
                                    {{ $savingsType->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('savings-types.show', $savingsType) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Detail</a>
                                    <a href="{{ route('savings-types.edit', $savingsType) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-outline">Belum ada jenis simpanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-outline-variant px-6 py-4">{{ $savingsTypes->links() }}</div>
    </form>
</x-app-layout>
