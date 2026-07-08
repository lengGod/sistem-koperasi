<x-app-layout>
    <x-slot name="header">
        <h2>Detail Barang</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Stok</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">{{ $product->name }}</h1>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                Kembali
            </a>
            <a href="{{ route('products.edit', $product) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined text-[20px]">edit</span>
                Edit Barang
            </a>
        </div>
    </section>

    <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-bold text-on-surface mb-4">Informasi Dasar</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-outline">Kode Barang (SKU)</dt>
                        <dd class="text-base font-bold text-on-surface">{{ $product->sku }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Nama Barang</dt>
                        <dd class="text-base font-bold text-on-surface">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Kategori</dt>
                        <dd class="text-base font-bold text-on-surface">{{ $product->category->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Tipe</dt>
                        <dd class="text-base font-bold text-on-surface capitalize">
                            {{ $product->type === 'general' ? 'Umum' : ucfirst($product->type) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-bold text-on-surface mb-4">Detail Stok & Lokasi</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-outline">Stok</dt>
                        <dd class="text-2xl font-extrabold text-on-surface">{{ $product->stock }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Harga Jual</dt>
                        <dd class="text-base font-bold text-on-surface">Rp.
                            {{ number_format($product->price, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Harga Modal</dt>
                        <dd class="text-base font-bold text-on-surface">Rp.
                            {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Satuan</dt>
                        <dd class="text-base font-bold text-on-surface">
                            {{ $product->unit ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Lokasi Penyimpanan</dt>
                        <dd class="text-base font-bold text-on-surface">{{ $product->location ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-outline">Tanggal Dibuat</dt>
                        <dd class="text-base font-bold text-on-surface">{{ $product->created_at->format('d M Y') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-outline-variant">
        <h3 class="text-lg font-bold text-on-surface mb-2">Keterangan</h3>
        <p class="text-sm text-on-surface-variant">{{ $product->description ?: 'Tidak ada keterangan.' }}</p>
    </div>
    </div>
</x-app-layout>
