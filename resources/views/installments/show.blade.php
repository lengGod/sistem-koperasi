<x-app-layout>
    <x-slot name="header">
        <h2>Detail Angsuran</h2>
    </x-slot>

    <section class="w-full">
        {{-- Breadcrumb --}}
        <nav class="mb-4 flex items-center gap-2 text-sm text-outline">
            <a href="{{ route('installments.index') }}" class="transition hover:text-primary">Angsuran</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-semibold text-on-surface">#{{ $installment->installment_number }}</span>
        </nav>

        {{-- Hero Profil --}}
        <div class="dashboard-card mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-container via-primary-container to-secondary-container">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between lg:p-8">
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-surface text-primary shadow-md">
                        <span class="material-symbols-outlined text-[40px]">event_repeat</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold tracking-tight text-on-primary-container lg:text-3xl">
                            Angsuran #{{ $installment->installment_number }}
                        </h1>
                        <div class="mt-2 text-sm text-on-primary-container/80">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                {{ $installment->loan?->loan_number ?? 'Pinjaman tidak dikenal' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('installments.edit', $installment) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                        <span class="material-symbols-outlined icon-fill text-[20px]">edit</span>
                        Edit
                    </a>
                    <a href="{{ route('installments.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="flex items-center gap-3 border-b border-outline-variant pb-4 mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-container text-on-primary-container">
                    <span class="material-symbols-outlined icon-fill">info</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-on-surface">Informasi Angsuran</h2>
                </div>
            </div>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Pinjaman</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $installment->loan?->loan_number ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Status</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-surface-container-high">
                            {{ ucfirst($installment->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jatuh Tempo</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ optional($installment->due_date)->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tagihan</dt>
                    <dd class="mt-1 text-sm font-bold text-on-surface">Rp {{ number_format((float) $installment->amount, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Dibayar</dt>
                    <dd class="mt-1 text-sm font-bold text-emerald-700">Rp {{ number_format((float) $installment->paid_amount, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal Bayar</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ optional($installment->paid_at)->format('d M Y') ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Pokok</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">Rp {{ number_format((float) $installment->principal_amount, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Bunga</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">Rp {{ number_format((float) $installment->interest_amount, 0, ',', '.') }}</dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Catatan</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface leading-6">{{ $installment->notes ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Aksi Cepat --}}
        <div class="mt-6 flex items-center justify-between rounded-2xl bg-surface-container-lowest px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Aksi Cepat</p>
                <p class="text-sm font-semibold text-on-surface">Hapus data angsuran ini.</p>
            </div>
            <form
                action="{{ route('installments.destroy', $installment) }}"
                method="POST"
                data-confirm="Anda akan menghapus data angsuran ini."
                data-confirm-title="Hapus angsuran"
                data-confirm-message="Tindakan ini tidak dapat dibatalkan."
                data-confirm-button="Ya, hapus"
                data-confirm-tone="danger"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Hapus
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
