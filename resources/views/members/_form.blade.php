@csrf

@php
    $isEdit = $member->exists;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama</label>
        <input id="name" name="name" value="{{ old('name', $member->name) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="account_number" class="mb-2 block text-sm font-bold text-on-surface">No Rekening</label>
        <input id="account_number" name="account_number" value="{{ old('account_number', $member->account_number) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('account_number') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="work_unit" class="mb-2 block text-sm font-bold text-on-surface">Unit Kerja</label>
        <input id="work_unit" name="work_unit" value="{{ old('work_unit', $member->work_unit) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('work_unit') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-bold text-on-surface">No Telp</label>
        <input id="phone" name="phone" value="{{ old('phone', $member->phone) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('phone') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="joined_at" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Bergabung</label>
        <input id="joined_at" name="joined_at" type="date" value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('joined_at') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status Keanggotaan</label>
        <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="active" @selected(old('status', $member->status) === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $member->status) === 'inactive')>Pasif</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="employment_status" class="mb-2 block text-sm font-bold text-on-surface">Status Pekerja</label>
        <input id="employment_status" name="employment_status" value="{{ old('employment_status', $member->employment_status) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('employment_status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ $isEdit ? route('members.show', $member) : route('members.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Anggota' }}
    </button>
</div>
