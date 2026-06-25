<x-app-layout>
    <x-slot name="header"><h2>Detail Pinjaman</h2></x-slot>
    <section class="w-full">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Inti Finansial</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">{{ $loan->loan_number }}</h1><p class="mt-1 text-sm text-outline">{{ $loan->member?->name ?? '-' }}</p></div>
            <div class="flex gap-2">
                <a href="{{ route('loans.edit', $loan) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">Edit</a>
                <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Kembali</a>
            </div>
        </div>
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Pokok</p><p class="mt-1 font-semibold">Rp {{ number_format((float) $loan->principal_amount, 0, ',', '.') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Bunga</p><p class="mt-1 font-semibold">{{ $loan->interest_rate }}%</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tenor</p><p class="mt-1 font-semibold">{{ $loan->term_months }} bulan</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Angsuran Bulanan</p><p class="mt-1 font-semibold">Rp {{ number_format((float) $loan->monthly_installment, 0, ',', '.') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Sisa Saldo</p><p class="mt-1 font-semibold">Rp {{ number_format((float) $loan->remaining_balance, 0, ',', '.') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Status</p><p class="mt-1 font-semibold">{{ ucfirst($loan->status) }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal Cair</p><p class="mt-1 font-semibold">{{ optional($loan->disbursed_at)->format('d M Y') ?: '-' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jatuh Tempo</p><p class="mt-1 font-semibold">{{ optional($loan->due_date)->format('d M Y') ?: '-' }}</p></div>
                <div class="md:col-span-2"><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Catatan</p><p class="mt-1 font-semibold leading-6">{{ $loan->notes ?: '-' }}</p></div>
            </div>
        </div>
    </section>
</x-app-layout>
