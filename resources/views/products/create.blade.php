<x-app-layout>
    <x-slot name="header">
        <h2>Tambah Barang</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Tambah Barang Baru</h1>
            <p class="mt-1 text-sm text-outline">Tambahkan item barang baru ke dalam sistem persediaan.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @include('products._form', ['product' => new \App\Models\Product])
        </form>
    </section>
</x-app-layout>
