<x-app-layout>
    <x-slot name="header">
        <h2>Kategori Barang</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Kategori Barang</h1>
            <p class="mt-1 text-sm text-outline">Kelola kategori untuk pengelompokan barang koperasi dan umum.</p>
        </div>

        <a href="{{ route('product-categories.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Kategori
        </a>
    </section>

    <form method="GET" action="{{ route('product-categories.index') }}"
        class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div>
            <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari Kategori</label>
            <div class="flex gap-2">
                <input id="search" name="search" value="{{ request('search') }}"
                    placeholder="Masukkan nama kategori..."
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                    Cari
                </button>
            </div>
        </div>
    </form>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest" x-data="{ selected: [] }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <h2 class="font-bold text-on-surface">Daftar Kategori</h2>
            <form action="{{ route('product-categories.bulk-destroy') }}" method="POST"
                x-show="selected.length > 0"
                x-transition
                data-confirm="Anda yakin ingin menghapus kategori yang dipilih? Tindakan ini tidak dapat dibatalkan."
                data-confirm-title="Hapus kategori masal" data-confirm-button="Ya, hapus"
                data-confirm-tone="danger">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="category_ids[]" :value="id">
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
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" @change="selected = $event.target.checked ? {{ $categories->pluck('id') }} : []"
                                class="rounded border-outline text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($categories as $category)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" value="{{ $category->id }}" x-model="selected"
                                    class="rounded border-outline text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $category->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('product-categories.edit', $category) }}"
                                        class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                        Edit
                                    </a>
                                    <form action="{{ route('product-categories.destroy', $category) }}" method="POST"
                                        data-confirm="Anda yakin ingin menghapus kategori {{ $category->name }}? Tindakan ini tidak dapat dibatalkan."
                                        data-confirm-title="Hapus kategori"
                                        data-confirm-button="Ya, hapus"
                                        data-confirm-tone="danger">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-error transition hover:bg-error-container">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
