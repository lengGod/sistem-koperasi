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

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($categories as $category)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $category->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('product-categories.edit', $category) }}"
                                        class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                        Edit
                                    </a>
                                    <form action="{{ route('product-categories.destroy', $category) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-error transition hover:bg-error-container"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
