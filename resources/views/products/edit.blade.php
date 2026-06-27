<x-app-layout>
    <x-slot name="header">
        <h2>Edit Barang</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Edit {{ $product->name }}</h1>
            <p class="mt-1 text-sm text-outline">Perbarui data barang dengan informasi terbaru.</p>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @method('PUT')
            @include('products._form', ['product' => $product])
        </form>
    </section>
</x-app-layout>
