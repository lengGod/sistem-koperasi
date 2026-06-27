<x-app-layout>
    <x-slot name="header">
        <h2>Detail Simpanan</h2>
    </x-slot>

    <section class="w-full">
        {{-- Breadcrumb --}}
        <nav class="mb-4 flex items-center gap-2 text-sm text-outline">
            <a href="{{ route('savings.index') }}" class="transition hover:text-primary">Simpanan</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-semibold text-on-surface">{{ $saving->reference_number ?: 'Detail Simpanan' }}</span>
        </nav>

        {{-- Hero Profil --}}
        <div class="dashboard-card mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-container via-primary-container to-secondary-container">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between lg:p-8">
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-surface text-primary shadow-md">
                        <span class="material-symbols-outlined text-[40px]">account_balance</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold tracking-tight text-on-primary-container lg:text-3xl">
                            {{ $saving->reference_number ?: 'Detail Simpanan' }}
                        </h1>
                        <div class="mt-2 text-sm text-on-primary-container/80">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">person</span>
                                {{ $saving->member?->name ?? 'Anggota tidak dikenal' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('savings.edit', $saving) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                        <span class="material-symbols-outlined icon-fill text-[20px]">edit</span>
                        Edit
                    </a>
                    <a href="{{ route('savings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
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
                    <h2 class="text-lg font-bold text-on-surface">Informasi Transaksi</h2>
                </div>
            </div>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Anggota</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $saving->member?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jenis Simpanan</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $saving->savingsType?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tipe</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $saving->transaction_type === 'deposit' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Nominal</dt>
                    <dd class="mt-1 text-sm font-bold text-on-surface">Rp {{ number_format((float) $saving->amount, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ optional($saving->transaction_date)->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Referensi</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $saving->reference_number ?: '-' }}</dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Catatan</dt>
                    <dd class="mt-1 text-sm font-semibold text-on-surface leading-6">{{ $saving->notes ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Aksi Cepat --}}
        <div class="mt-6 flex items-center justify-between rounded-2xl bg-surface-container-lowest px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Aksi Cepat</p>
                <p class="text-sm font-semibold text-on-surface">Hapus data simpanan ini.</p>
            </div>
            <form
                action="{{ route('savings.destroy', $saving) }}"
                method="POST"
                data-confirm="Anda akan menghapus data simpanan ini."
                data-confirm-title="Hapus simpanan"
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
