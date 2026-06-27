@csrf

@php $isEdit = $installment->exists; @endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="loan_id" class="mb-2 block text-sm font-bold text-on-surface">Pinjaman</label>
        <select id="loan_id" name="loan_id" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            <option value="">Pilih pinjaman</option>
            @foreach ($loans as $loan)
                <option value="{{ $loan->id }}" @selected((string) old('loan_id', $installment->loan_id) === (string) $loan->id)>{{ $loan->loan_number }} - {{ $loan->member?->name ?? '-' }}</option>
            @endforeach
        </select>
        @error('loan_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="due_date" class="mb-2 block text-sm font-bold text-on-surface">Jatuh Tempo</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', optional($installment->due_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('due_date') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
        <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            @foreach (['pending' => 'Pending', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'late' => 'Terlambat'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $installment->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="principal_amount" class="mb-2 block text-sm font-bold text-on-surface">Pokok</label>
        <input id="principal_amount" name="principal_amount" type="number" step="0.01" value="{{ old('principal_amount', $installment->principal_amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('principal_amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="interest_amount" class="mb-2 block text-sm font-bold text-on-surface">Bunga</label>
        <input id="interest_amount" name="interest_amount" type="number" step="0.01" value="{{ old('interest_amount', $installment->interest_amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('interest_amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="amount" class="mb-2 block text-sm font-bold text-on-surface">Total Tagihan</label>
        <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount', $installment->amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
        @error('amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="paid_amount" class="mb-2 block text-sm font-bold text-on-surface">Jumlah Dibayar</label>
        <input id="paid_amount" name="paid_amount" type="number" step="0.01" value="{{ old('paid_amount', $installment->paid_amount) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('paid_amount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="paid_at" class="mb-2 block text-sm font-bold text-on-surface">Tanggal Bayar</label>
        <input id="paid_at" name="paid_at" type="date" value="{{ old('paid_at', optional($installment->paid_at)->format('Y-m-d')) }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
        @error('paid_at') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="mb-2 block text-sm font-bold text-on-surface">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">{{ old('notes', $installment->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap justify-end gap-3">
    <a href="{{ $isEdit ? route('installments.show', $installment) : route('installments.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
        <span class="material-symbols-outlined icon-fill text-[20px]">save</span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Angsuran' }}
    </button>
</div>
