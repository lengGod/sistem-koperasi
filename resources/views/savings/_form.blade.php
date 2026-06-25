@csrf

@php $isEdit = $saving->exists; @endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="member_id" class="mb-2 block text-sm font-bold text-on-surface">Anggota</label>
        <select id="member_id" name="member_id" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="">Pilih anggota</option>
            @foreach ($members as $member)
                <option value="{{ $member->id }}" @selected((string) old('member_id', $saving->member_id) === (string) $member->id)>{{ $member->member_number }} - {{ $member->name }}</option>
            @endforeach
        </select>
        @error('member_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="savings_type_id" class="mb-2 block text-sm font-bold text-on-surface">Jenis Simpanan</label>
        <select id="savings_type_id" name="savings_type_id" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="">Pilih jenis</option>
            @foreach ($savingsTypes as $type)
                <option value="{{ $type->id }}" @selected((string) old('savings_type_id', $saving->savings_type_id) === (string) $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
        @error('savings_type_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="transaction_type" class="mb-2 block text-sm font-bold text-on-surface">Tipe Transaksi</label>
        <select id="transaction_type" name="transaction_type" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="deposit" @selected(old('transaction_type', $saving->transaction_type) === 'deposit')>Setoran</option>
            <option value="withdrawal" @selected(old('transaction_type', $saving->transaction_type) === 'withdrawal')>Penarikan</option>
        </select>
        @error('transaction_type') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="amount" class="mb-2 block text-sm font-bold text-on-surface">Nominal</label>
        <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount', $saving->amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="transaction_date" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Transaksi</label>
        <input id="transaction_date" name="transaction_date" type="date" value="{{ old('transaction_date', optional($saving->transaction_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('transaction_date') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="reference_number" class="mb-2 block text-sm font-bold text-on-surface">Referensi</label>
        <input id="reference_number" name="reference_number" value="{{ old('reference_number', $saving->reference_number) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('reference_number') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="mb-2 block text-sm font-bold text-on-surface">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('notes', $saving->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ $isEdit ? route('savings.show', $saving) : route('savings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Simpanan' }}
    </button>
</div>
