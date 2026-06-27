<x-app-layout>
    <x-slot name="header">
        <h2>Tambah Kategori</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Tambah Kategori Baru</h1>
            <p class="mt-1 text-sm text-outline">Tambahkan kategori barang baru untuk pengelompokan yang lebih rapi.</p>
        </div>

        <form action="{{ route('product-categories.store') }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @include('product-categories._form', ['category' => new \App\Models\ProductCategory])
        </form>
    </section>
</x-app-layout>
