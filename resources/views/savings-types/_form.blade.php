@csrf

@php
    $isEdit = $savingsType->exists;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="code" class="mb-2 block text-sm font-bold text-on-surface">Kode</label>
        <input id="code" name="code" value="{{ old('code', $savingsType->code) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('code') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama</label>
        <input id="name" name="name" value="{{ old('name', $savingsType->name) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-bold text-on-surface">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('description', $savingsType->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-on-surface">Wajib</label>
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-3 text-sm font-bold text-on-surface-variant">
            <input type="checkbox" name="is_mandatory" value="1" class="rounded border-outline-variant text-primary focus:ring-primary" @checked(old('is_mandatory', $savingsType->is_mandatory))>
            Tipe ini wajib
        </label>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-on-surface">Aktif</label>
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-3 text-sm font-bold text-on-surface-variant">
            <input type="checkbox" name="is_active" value="1" class="rounded border-outline-variant text-primary focus:ring-primary" @checked(old('is_active', $savingsType->is_active ?? true))>
            Tipe aktif
        </label>
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ $isEdit ? route('savings-types.show', $savingsType) : route('savings-types.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jenis' }}
    </button>
</div>
