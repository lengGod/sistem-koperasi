<x-app-layout>
    <x-slot name="header">
        <h2>Stok Barang</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Daftar Barang</h1>
            <p class="mt-1 text-sm text-outline">Kelola data persediaan barang koperasi dan umum.</p>
        </div>

        <a href="{{ route('products.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Barang
        </a>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4 text-right">Harga</th>
                        <th class="px-6 py-4 text-center">Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-mono text-xs">{{ $product->sku }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 capitalize">{{ $product->type }}</td>
                            <td class="px-6 py-4 text-right font-bold text-on-surface">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="{{ $product->stock < 10 ? 'text-error font-bold' : 'text-on-surface-variant' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                        Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST">
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
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada data barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
