@csrf

@php
    $isEdit = $product->exists;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama Barang</label>
        <input id="name" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sku" class="mb-2 block text-sm font-bold text-on-surface">SKU</label>
        <input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('sku') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="category_id" class="mb-2 block text-sm font-bold text-on-surface">Kategori</label>
        <select id="category_id" name="category_id" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            <option value="">Pilih Kategori</option>
            @foreach (\App\Models\ProductCategory::all() as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="type" class="mb-2 block text-sm font-bold text-on-surface">Tipe</label>
        <select id="type" name="type" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="general" @selected(old('type', $product->type) === 'general')>Umum</option>
            <option value="koperasi" @selected(old('type', $product->type) === 'koperasi')>Koperasi</option>
        </select>
        @error('type') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="price" class="mb-2 block text-sm font-bold text-on-surface">Harga Jual</label>
        <input id="price" name="price" type="number" value="{{ old('price', (int)$product->price) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('price') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="purchase_price" class="mb-2 block text-sm font-bold text-on-surface">Harga Modal</label>
        <input id="purchase_price" name="purchase_price" type="number" value="{{ old('purchase_price', (int)$product->purchase_price) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('purchase_price') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="stock" class="mb-2 block text-sm font-bold text-on-surface">Stok</label>
        <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('stock') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="unit" class="mb-2 block text-sm font-bold text-on-surface">Satuan</label>
        <input id="unit" name="unit" value="{{ old('unit', $product->unit) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('unit') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="location" class="mb-2 block text-sm font-bold text-on-surface">Lokasi Penyimpanan</label>
        <input id="location" name="location" value="{{ old('location', $product->location) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('location') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-bold text-on-surface">Keterangan</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Barang' }}
    </button>
</div>
