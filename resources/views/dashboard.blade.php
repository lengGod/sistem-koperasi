<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard</h2>
    </x-slot>

    <section class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Ringkasan Hari Ini</p>
            <h2 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Admin Dashboard</h2>
            <p class="mt-1 text-sm text-outline">Pantau anggota, simpanan, pinjaman, dan angsuran koperasi dari satu layar.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-primary shadow-sm transition hover:bg-primary-fixed">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Tambah Anggota
            </a>
            <a href="{{ route('savings.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">account_balance</span>
                Catat Simpanan
            </a>
        </div>
    </section>

    <section class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Anggota', 'value' => number_format($totalMembers), 'icon' => 'group', 'tone' => 'bg-primary-fixed text-primary', 'note' => 'Anggota terdaftar'],
            ['label' => 'Total Simpanan', 'value' => 'Rp '.number_format($totalSavings, 0, ',', '.'), 'icon' => 'account_balance', 'tone' => 'bg-secondary-fixed text-secondary', 'note' => 'Akumulasi seluruh transaksi'],
            ['label' => 'Pinjaman Aktif', 'value' => number_format($activeLoans), 'icon' => 'payments', 'tone' => 'bg-tertiary-fixed text-tertiary', 'note' => 'Pinjaman berjalan'],
            ['label' => 'Angsuran Perlu Proses', 'value' => number_format($dueInstallments), 'icon' => 'event_repeat', 'tone' => 'bg-surface-container-high text-on-surface', 'note' => 'Pending, partial, late'],
        ] as $card)
            <article class="dashboard-card rounded-3xl border border-white/70 bg-surface-container-lowest p-6">
                <div class="mb-5 flex items-start justify-between">
                    <div class="{{ $card['tone'] }} flex h-12 w-12 items-center justify-center rounded-2xl">
                        <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
                    </div>
                    <span class="rounded-full bg-surface-container-low px-2.5 py-1 text-[11px] font-extrabold text-on-surface-variant">Laporan</span>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">{{ $card['label'] }}</p>
                <h3 class="mt-1 text-3xl font-extrabold tracking-tight text-on-surface">{{ $card['value'] }}</h3>
                <p class="mt-2 text-sm text-outline">{{ $card['note'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-3">
        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-fixed text-secondary">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <span class="rounded-full bg-surface-container-low px-2.5 py-1 text-[11px] font-extrabold text-on-surface-variant">Bulan ini</span>
            </div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Simpanan Masuk</p>
            <h3 class="mt-1 text-3xl font-extrabold tracking-tight text-on-surface">Rp {{ number_format($totalSavingsThisMonth, 0, ',', '.') }}</h3>
            <p class="mt-2 text-sm text-outline">Total transaksi simpanan pada bulan berjalan.</p>
        </article>

        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-fixed text-primary">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <span class="rounded-full bg-surface-container-low px-2.5 py-1 text-[11px] font-extrabold text-on-surface-variant">Saldo berjalan</span>
            </div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Saldo Pinjaman Aktif</p>
            <h3 class="mt-1 text-3xl font-extrabold tracking-tight text-on-surface">Rp {{ number_format($activeLoanBalance, 0, ',', '.') }}</h3>
            <p class="mt-2 text-sm text-outline">Sisa pokok pinjaman yang masih aktif.</p>
        </article>

        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-tertiary-fixed text-tertiary">
                    <span class="material-symbols-outlined">event_repeat</span>
                </div>
                <span class="rounded-full bg-surface-container-low px-2.5 py-1 text-[11px] font-extrabold text-on-surface-variant">Status angsuran</span>
            </div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Angsuran Lunas / Terlambat</p>
            <h3 class="mt-1 text-3xl font-extrabold tracking-tight text-on-surface">{{ number_format($paidInstallments) }} / {{ number_format($overdueInstallments) }}</h3>
            <p class="mt-2 text-sm text-outline">Perbandingan angsuran lunas dan yang melewati jatuh tempo.</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6 xl:col-span-8">
            <div class="mb-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h3 class="text-xl font-bold text-on-surface">Grafik Simpanan & Angsuran Bulanan</h3>
                    <p class="text-sm text-outline">Data 6 bulan terakhir dari transaksi yang sudah tersimpan.</p>
                </div>
                <div class="rounded-full bg-surface-container-low px-3 py-1 text-xs font-bold text-on-surface-variant">
                    Total maksimum: Rp {{ number_format($maxTotal, 0, ',', '.') }}
                </div>
            </div>

            <div class="rounded-2xl bg-surface-container-low p-5">
                <div class="flex h-72 items-end gap-3 sm:gap-5">
                    @foreach ($chartBars as $bar)
                        @php
                            $savingsHeight = max(4, ($bar['savings'] / $maxTotal) * 100);
                            $installmentsHeight = max(4, ($bar['installments'] / $maxTotal) * 100);
                        @endphp
                        <div class="group flex min-w-0 flex-1 flex-col items-center justify-end gap-3">
                            <div class="flex w-full items-end gap-1">
                                <div class="relative flex-1 rounded-t-xl bg-primary-container/35 transition group-hover:bg-primary-container" style="height: {{ $savingsHeight }}%">
                                    <span class="absolute -top-8 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-on-surface px-2 py-1 text-[10px] font-bold text-inverse-on-surface group-hover:block">
                                        Rp {{ number_format($bar['savings'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="relative flex-1 rounded-t-xl bg-tertiary-fixed/80 transition group-hover:bg-tertiary" style="height: {{ $installmentsHeight }}%">
                                    <span class="absolute -top-8 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-on-surface px-2 py-1 text-[10px] font-bold text-inverse-on-surface group-hover:block">
                                        Rp {{ number_format($bar['installments'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-outline">{{ $bar['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex flex-wrap gap-4 text-xs font-bold text-on-surface-variant">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-primary-container"></span>Simpanan</span>
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-tertiary"></span>Angsuran</span>
                </div>
            </div>
        </article>

        <article class="dashboard-card rounded-3xl bg-surface-container-lowest p-6 xl:col-span-4">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-on-surface">Transaksi Terbaru</h3>
                <a href="{{ route('savings.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-5">
                @forelse ($recentSavings as $saving)
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <span class="material-symbols-outlined text-[20px]">account_balance</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm leading-5 text-on-surface">
                                <strong>{{ $saving->member?->name ?? '-' }}</strong> {{ $saving->transaction_type === 'deposit' ? 'menyetor' : 'menarik' }} Rp {{ number_format((float) $saving->amount, 0, ',', '.') }}
                            </p>
                            <p class="mt-1 text-xs text-outline">{{ optional($saving->transaction_date)->format('d M Y') }} &middot; {{ $saving->savingsType?->name ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-outline">Belum ada transaksi simpanan.</p>
                @endforelse
            </div>

            <div class="mt-8 border-t border-outline-variant pt-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-on-surface">Pinjaman Terbaru</h3>
                    <a href="{{ route('loans.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-5">
                    @forelse ($recentLoans as $loan)
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                <span class="material-symbols-outlined text-[20px]">payments</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm leading-5 text-on-surface">
                                    <strong>{{ $loan->member?->name ?? '-' }}</strong> - {{ $loan->loan_number }}
                                </p>
                                <p class="mt-1 text-xs text-outline">Rp {{ number_format((float) $loan->principal_amount, 0, ',', '.') }} &middot; {{ ucfirst($loan->status) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-outline">Belum ada data pinjaman.</p>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest xl:col-span-12">
            <div class="flex flex-col justify-between gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center">
                <div>
                    <h3 class="text-xl font-bold text-on-surface">Angsuran Mendatang</h3>
                    <p class="text-sm text-outline">Daftar pembayaran yang perlu diproses oleh admin.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('installments.index') }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                        Buka Angsuran
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4">Nama Anggota</th>
                            <th class="px-6 py-4">ID Pinjaman</th>
                            <th class="px-6 py-4">Jatuh Tempo</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($upcomingInstallments as $installment)
                            <tr class="transition hover:bg-surface-container">
                                <td class="px-6 py-4 font-bold text-on-surface">{{ $installment->loan?->member?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-on-surface-variant">{{ $installment->loan?->loan_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-on-surface-variant">{{ optional($installment->due_date)->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-semibold text-on-surface">Rp {{ number_format((float) $installment->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $installment->status === 'late' ? 'bg-error-container text-on-error-container' : 'bg-yellow-100 text-yellow-800' }} rounded-full px-2.5 py-1 text-xs font-extrabold">
                                        {{ ucfirst($installment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('installments.show', $installment) }}" class="font-bold text-primary hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-outline">
                                    Tidak ada angsuran yang perlu diproses.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</x-app-layout>
