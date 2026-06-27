<x-app-layout>
    <x-slot name="header">
        <h2>Detail Anggota</h2>
    </x-slot>

    <section class="w-full" x-data="{ tab: 'savings' }">
        {{-- Breadcrumb --}}
        <nav class="mb-4 flex items-center gap-2 text-sm text-outline">
            <a href="{{ route('members.index') }}" class="transition hover:text-primary">Anggota</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-semibold text-on-surface">{{ $member->name }}</span>
        </nav>

        {{-- Hero Profil --}}
        <div class="dashboard-card mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-container via-primary-container to-secondary-container">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between lg:p-8">
                <div class="flex items-start gap-5">
                    {{-- Avatar inisial --}}
                    @php
                        $initials = collect(explode(' ', $member->name))
                            ->map(fn ($part) => mb_substr($part, 0, 1))
                            ->take(2)
                            ->implode('');
                        $initials = mb_strtoupper($initials);
                    @endphp
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-surface text-2xl font-extrabold text-primary shadow-md">
                        {{ $initials }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-on-primary-container lg:text-3xl">
                                {{ $member->name }}
                            </h1>
                            @include('members._status_badge', ['status' => $member->status])
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-on-primary-container/80">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">badge</span>
                                {{ $member->account_number ?: 'Belum ada no rekening' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">apartment</span>
                                {{ $member->work_unit ?: 'Unit kerja belum diisi' }}
                            </span>
                            @if ($member->joined_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">event</span>
                                    Bergabung {{ $member->joined_at->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                        <span class="material-symbols-outlined icon-fill text-[20px]">edit</span>
                        Edit
                    </a>
                    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Quick stats inline --}}
            <div class="grid grid-cols-2 gap-px border-t border-outline-variant/40 bg-outline-variant/40 lg:grid-cols-4">
                <div class="bg-surface-container-lowest px-6 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Total Simpanan</p>
                    <p class="mt-1 text-xl font-bold tracking-tight text-on-surface">
                        Rp {{ number_format($savingsBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-surface-container-lowest px-6 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Pinjaman Aktif</p>
                    <p class="mt-1 text-xl font-bold tracking-tight text-on-surface">
                        {{ $activeLoansCount }}
                        <span class="text-xs font-normal text-outline">/ {{ $loans->count() }} total</span>
                    </p>
                </div>
                <div class="bg-surface-container-lowest px-6 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Sisa Pinjaman</p>
                    <p class="mt-1 text-xl font-bold tracking-tight text-on-surface">
                        Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-surface-container-lowest px-6 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Angsuran Telat</p>
                    <p class="mt-1 text-xl font-bold tracking-tight {{ $overdueInstallmentsCount > 0 ? 'text-error' : 'text-on-surface' }}">
                        {{ $overdueInstallmentsCount }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Kartu Identitas --}}
            <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6 lg:col-span-2">
                <div class="flex items-center gap-3 border-b border-outline-variant pb-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-container text-on-primary">
                        <span class="material-symbols-outlined icon-fill">id_card</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Identitas Anggota</h2>
                        <p class="text-xs text-outline">Informasi personal dan data keanggotaan.</p>
                    </div>
                </div>
                <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">NIK</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $member->nik ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">
                            {{ $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '-') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">
                            {{ $member->birth_place ?: '-' }}{{ $member->birth_date ? ', ' . $member->birth_date->format('d M Y') : '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">No Telp</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $member->phone ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Email</dt>
                        <dd class="mt-1 break-all text-sm font-semibold text-on-surface">{{ $member->email ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Status Pekerja</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $member->employment_status ?: '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Alamat</dt>
                        <dd class="mt-1 text-sm font-semibold text-on-surface">{{ $member->address ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Kartu Ringkasan Pinjaman --}}
            <div class="space-y-4">
                <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
                    <div class="flex items-center gap-3 border-b border-outline-variant pb-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <span class="material-symbols-outlined icon-fill">payments</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-on-surface">Pinjaman</h2>
                            <p class="text-xs text-outline">Status dan sisa pinjaman.</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Aktif</span>
                            <span class="text-lg font-bold text-on-surface">{{ $activeLoansCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Sisa Pinjaman</span>
                            <span class="text-lg font-bold text-amber-700">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Total Pinjaman</span>
                            <span class="text-lg font-bold text-on-surface">{{ $loans->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
                    <div class="flex items-center gap-3 border-b border-outline-variant pb-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <span class="material-symbols-outlined icon-fill">event_repeat</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-on-surface">Angsuran</h2>
                            <p class="text-xs text-outline">Progress pembayaran cicilan.</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Jatuh Tempo</span>
                            <span class="text-lg font-bold text-on-surface">{{ $dueInstallmentsCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Telat</span>
                            <span class="text-lg font-bold {{ $overdueInstallmentsCount > 0 ? 'text-error' : 'text-on-surface' }}">{{ $overdueInstallmentsCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-outline">Lunas</span>
                            <span class="text-lg font-bold text-emerald-700">{{ $paidInstallmentsCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Simpanan per Jenis --}}
        @if ($savingsByType->isNotEmpty())
            <div class="dashboard-card mt-6 rounded-3xl bg-surface-container-lowest p-6">
                <div class="flex items-center justify-between border-b border-outline-variant pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Saldo per Jenis Simpanan</h2>
                        <p class="text-xs text-outline">Akumulasi setoran dan penarikan per jenis.</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($savingsByType as $row)
                        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-low p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">
                                {{ $row->savingsType?->name ?? 'Tanpa Jenis' }}
                            </p>
                            <p class="mt-2 text-xl font-bold text-on-surface">
                                Rp {{ number_format((float) $row->balance, 0, ',', '.') }}
                            </p>
                            <p class="mt-1 text-xs text-outline">{{ $row->total_tx }} transaksi</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="dashboard-card mt-6 overflow-hidden rounded-3xl bg-surface-container-lowest">
            <div class="border-b border-outline-variant">
                <nav class="flex overflow-x-auto" role="tablist">
                    @php
                        $tabs = [
                            'savings'     => ['label' => 'Simpanan',     'icon' => 'account_balance', 'count' => $member->savings->count()],
                            'loans'       => ['label' => 'Pinjaman',     'icon' => 'payments',        'count' => $loans->count()],
                            'installments'=> ['label' => 'Angsuran',     'icon' => 'event_repeat',    'count' => $installments->count()],
                        ];
                    @endphp
                    @foreach ($tabs as $key => $tab)
                        <button
                            type="button"
                            role="tab"
                            :class="tab === '{{ $key }}' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface'"
                            @click="tab = '{{ $key }}'"
                            class="flex shrink-0 items-center gap-2 border-b-2 px-6 py-4 text-sm font-bold transition"
                        >
                            <span class="material-symbols-outlined text-[20px]">{{ $tab['icon'] }}</span>
                            {{ $tab['label'] }}
                            <span class="rounded-full bg-surface-container-low px-2 py-0.5 text-xs">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Tab Simpanan --}}
            <div x-show="tab === 'savings'" x-cloak class="p-6">
                @if ($recentSavings->isEmpty())
                    <p class="py-8 text-center text-sm text-outline">Belum ada transaksi simpanan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Jenis Simpanan</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/30">
                                @foreach ($recentSavings as $saving)
                                    <tr class="hover:bg-surface-container-low/50">
                                        <td class="px-4 py-3 font-semibold">{{ $saving->transaction_date->format('d M Y') }}</td>
                                        <td class="px-4 py-3">{{ $saving->savingsType?->name ?? '-' }}</td>
                                        <td class="px-4 py-3">@include('members._status_badge', ['status' => $saving->transaction_type])</td>
                                        <td class="px-4 py-3 text-right font-bold {{ $saving->transaction_type === 'deposit' ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $saving->transaction_type === 'withdrawal' ? '-' : '+' }}Rp {{ number_format((float) $saving->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-outline">{{ $saving->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab Pinjaman + Jadwal Angsuran --}}
            <div x-show="tab === 'loans'" x-cloak class="p-6">
                @if ($loans->isEmpty())
                    <p class="py-8 text-center text-sm text-outline">Belum ada pinjaman.</p>
                @else
                    <div class="space-y-4" x-data="{ openId: null }">
                        @foreach ($loans as $loan)
                            <div class="overflow-hidden rounded-2xl border border-outline-variant/40 bg-surface-container-low">
                                <button
                                    type="button"
                                    @click="openId = openId === {{ $loan->id }} ? null : {{ $loan->id }}"
                                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left transition hover:bg-surface-container"
                                >
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container text-on-primary">
                                            <span class="material-symbols-outlined icon-fill">payments</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-on-surface">{{ $loan->loan_number }}</p>
                                            <p class="text-xs text-outline">
                                                Pokok Rp {{ number_format((float) $loan->principal_amount, 0, ',', '.') }} ·
                                                Tenor {{ $loan->term_months }} bln ·
                                                Sisa Rp {{ number_format((float) $loan->remaining_balance, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @include('members._status_badge', ['status' => $loan->status])
                                        <span class="material-symbols-outlined transition" :class="openId === {{ $loan->id }} ? 'rotate-180' : ''">expand_more</span>
                                    </div>
                                </button>

                                <div x-show="openId === {{ $loan->id }}" x-cloak class="border-t border-outline-variant/40 bg-surface-container-lowest p-5">
                                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal Cair</p>
                                            <p class="mt-1 text-sm font-semibold">{{ optional($loan->disbursed_at)->format('d M Y') ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jatuh Tempo</p>
                                            <p class="mt-1 text-sm font-semibold">{{ optional($loan->due_date)->format('d M Y') ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Angsuran / Bulan</p>
                                            <p class="mt-1 text-sm font-semibold">Rp {{ number_format((float) $loan->monthly_installment, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Bunga</p>
                                            <p class="mt-1 text-sm font-semibold">{{ $loan->interest_rate }}%</p>
                                        </div>
                                    </div>

                                    <h3 class="mt-6 text-sm font-bold text-on-surface">Jadwal Angsuran</h3>
                                    @if ($loan->installments->isEmpty())
                                        <p class="mt-3 text-sm text-outline">Belum ada jadwal angsuran.</p>
                                    @else
                                        <div class="mt-3 overflow-x-auto">
                                            <table class="w-full min-w-[640px] text-left text-sm">
                                                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                                                    <tr>
                                                        <th class="px-3 py-2">#</th>
                                                        <th class="px-3 py-2">Jatuh Tempo</th>
                                                        <th class="px-3 py-2 text-right">Pokok</th>
                                                        <th class="px-3 py-2 text-right">Bunga</th>
                                                        <th class="px-3 py-2 text-right">Total</th>
                                                        <th class="px-3 py-2 text-right">Dibayar</th>
                                                        <th class="px-3 py-2">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-outline-variant/30">
                                                    @foreach ($loan->installments as $installment)
                                                        <tr class="hover:bg-surface-container-low/40">
                                                            <td class="px-3 py-2 font-semibold">{{ $installment->installment_number }}</td>
                                                            <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                                                            <td class="px-3 py-2 text-right">Rp {{ number_format((float) $installment->principal_amount, 0, ',', '.') }}</td>
                                                            <td class="px-3 py-2 text-right">Rp {{ number_format((float) $installment->interest_amount, 0, ',', '.') }}</td>
                                                            <td class="px-3 py-2 text-right font-bold">Rp {{ number_format((float) $installment->amount, 0, ',', '.') }}</td>
                                                            <td class="px-3 py-2 text-right">{{ $installment->paid_amount > 0 ? 'Rp ' . number_format((float) $installment->paid_amount, 0, ',', '.') : '-' }}</td>
                                                            <td class="px-3 py-2">@include('members._status_badge', ['status' => $installment->status])</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tab Angsuran --}}
            <div x-show="tab === 'installments'" x-cloak class="p-6">
                @if ($installments->isEmpty())
                    <p class="py-8 text-center text-sm text-outline">Belum ada data angsuran.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-left text-sm">
                            <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3">No Pinjaman</th>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3 text-right">Dibayar</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/30">
                                @foreach ($installments as $installment)
                                    <tr class="hover:bg-surface-container-low/50">
                                        <td class="px-4 py-3 font-semibold">{{ $installment->loan?->loan_number ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $installment->installment_number }}</td>
                                        <td class="px-4 py-3">{{ $installment->due_date->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-right font-bold">Rp {{ number_format((float) $installment->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right">{{ $installment->paid_amount > 0 ? 'Rp ' . number_format((float) $installment->paid_amount, 0, ',', '.') : '-' }}</td>
                                        <td class="px-4 py-3">@include('members._status_badge', ['status' => $installment->status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="mt-6 flex items-center justify-between rounded-2xl bg-surface-container-lowest px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Aksi Cepat</p>
                <p class="text-sm font-semibold text-on-surface">Hapus data anggota ini jika sudah berstatus pasif.</p>
            </div>
            <form
                action="{{ route('members.destroy', $member) }}"
                method="POST"
                data-confirm="Anda akan menghapus data anggota ini."
                data-confirm-title="Hapus anggota"
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