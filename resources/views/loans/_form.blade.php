@csrf

@php $isEdit = $loan->exists; @endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="member_id" class="mb-2 block text-sm font-bold text-on-surface">Anggota</label>
        <select id="member_id" name="member_id" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="">Pilih anggota</option>
            @foreach ($members as $member)
                <option value="{{ $member->id }}" @selected((string) old('member_id', $loan->member_id) === (string) $member->id)>{{ $member->member_number }} - {{ $member->name }}</option>
            @endforeach
        </select>
        @error('member_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="loan_number" class="mb-2 block text-sm font-bold text-on-surface">Nomor Pinjaman</label>
        <input id="loan_number" name="loan_number" value="{{ old('loan_number', $loan->loan_number) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('loan_number') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="principal_amount" class="mb-2 block text-sm font-bold text-on-surface">Pokok Pinjaman</label>
        <input id="principal_amount" name="principal_amount" type="number" step="0.01" value="{{ old('principal_amount', $loan->principal_amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('principal_amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="interest_rate" class="mb-2 block text-sm font-bold text-on-surface">Bunga (%)</label>
        <input id="interest_rate" name="interest_rate" type="number" step="0.01" value="{{ old('interest_rate', $loan->interest_rate) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('interest_rate') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="term_months" class="mb-2 block text-sm font-bold text-on-surface">Tenor (Bulan)</label>
        <input id="term_months" name="term_months" type="number" value="{{ old('term_months', $loan->term_months) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('term_months') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="monthly_installment" class="mb-2 block text-sm font-bold text-on-surface">Angsuran Bulanan</label>
        <input id="monthly_installment" name="monthly_installment" type="number" step="0.01" value="{{ old('monthly_installment', $loan->monthly_installment) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('monthly_installment') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="remaining_balance" class="mb-2 block text-sm font-bold text-on-surface">Sisa Saldo</label>
        <input id="remaining_balance" name="remaining_balance" type="number" step="0.01" value="{{ old('remaining_balance', $loan->remaining_balance ?? $loan->principal_amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('remaining_balance') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
        <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Lunas', 'overdue' => 'Terlambat', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $loan->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="disbursed_at" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Cair</label>
        <input id="disbursed_at" name="disbursed_at" type="date" value="{{ old('disbursed_at', optional($loan->disbursed_at)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('disbursed_at') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="due_date" class="mb-2 block text-sm font-bold text-on-surface">Jatuh Tempo</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', optional($loan->due_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('due_date') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="mb-2 block text-sm font-bold text-on-surface">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('notes', $loan->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ $isEdit ? route('loans.show', $loan) : route('loans.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pinjaman' }}
    </button>
</div>
