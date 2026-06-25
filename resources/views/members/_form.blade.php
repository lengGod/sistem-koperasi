@csrf

@php
    $isEdit = $member->exists;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="member_number" class="mb-2 block text-sm font-bold text-on-surface">Nomor Anggota</label>
        <input id="member_number" name="member_number" value="{{ old('member_number', $member->member_number) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('member_number') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nik" class="mb-2 block text-sm font-bold text-on-surface">NIK</label>
        <input id="nik" name="nik" value="{{ old('nik', $member->nik) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('nik') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama Lengkap</label>
        <input id="name" name="name" value="{{ old('name', $member->name) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="gender" class="mb-2 block text-sm font-bold text-on-surface">Jenis Kelamin</label>
        <select id="gender" name="gender" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="male" @selected(old('gender', $member->gender) === 'male')>Laki-laki</option>
            <option value="female" @selected(old('gender', $member->gender) === 'female')>Perempuan</option>
        </select>
        @error('gender') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
        <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="active" @selected(old('status', $member->status) === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $member->status) === 'inactive')>Tidak Aktif</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="birth_place" class="mb-2 block text-sm font-bold text-on-surface">Tempat Lahir</label>
        <input id="birth_place" name="birth_place" value="{{ old('birth_place', $member->birth_place) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('birth_place') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="birth_date" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Lahir</label>
        <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', optional($member->birth_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('birth_date') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-bold text-on-surface">No. Telepon</label>
        <input id="phone" name="phone" value="{{ old('phone', $member->phone) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('phone') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-bold text-on-surface">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $member->email) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('email') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="joined_at" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Bergabung</label>
        <input id="joined_at" name="joined_at" type="date" value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('joined_at') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="address" class="mb-2 block text-sm font-bold text-on-surface">Alamat</label>
        <textarea id="address" name="address" rows="4" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('address', $member->address) }}</textarea>
        @error('address') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
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
