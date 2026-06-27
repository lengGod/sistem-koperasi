@csrf

@php
    $isEdit = $category->exists;
@endphp

<div class="grid grid-cols-1 gap-5">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama Kategori</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-bold text-on-surface">Slug (URL Friendly)</label>
        <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('slug') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-bold text-on-surface">Deskripsi</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('description', $category->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ route('product-categories.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
    </button>
</div>
