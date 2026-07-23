<x-app-layout>
    <x-slot name="header">
        <h2>Laporan Koperasi</h2>
    </x-slot>

    <div class="mb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Laporan</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Laporan Koperasi</h1>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('reports.koperasi.index') }}" method="GET" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="start_month" class="mb-2 block text-sm font-bold text-on-surface">Dari Bulan</label>
                <input type="month" id="start_month" name="start_month" value="{{ $start_month }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary text-sm">
            </div>
            <div>
                <label for="end_month" class="mb-2 block text-sm font-bold text-on-surface">Sampai Bulan</label>
                <input type="month" id="end_month" name="end_month" value="{{ $end_month }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition">
                    Filter
                </button>
                <button type="button"
                    onclick="window.location.href='{{ route('reports.koperasi.export', ['start_month' => $start_month, 'end_month' => $end_month], false) }}'"
                    class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-primary-container px-4 py-2.5 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Export
                </button>
            </div>
        </div>
    </form>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead
                    class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Anggota</td>
                        <td class="px-6 py-4 text-on-surface-variant">Total anggota terdaftar</td>
                        <td class="px-6 py-4 text-right font-semibold">{{ number_format($totalMembers) }}</td>
                    </tr>
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Simpanan</td>
                        <td class="px-6 py-4 text-on-surface-variant">Total akumulasi simpanan</td>
                        <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($totalSavings, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Pinjaman</td>
                        <td class="px-6 py-4 text-on-surface-variant">Total pokok pinjaman diberikan</td>
                        <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($totalLoans, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Angsuran</td>
                        <td class="px-6 py-4 text-on-surface-variant">Total angsuran lunas diterima</td>
                        <td class="px-6 py-4 text-right font-semibold">Rp
                            {{ number_format($totalInstallmentsPaid, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Status</td>
                        <td class="px-6 py-4 text-on-surface-variant">Jumlah angsuran terlambat</td>
                        <td class="px-6 py-4 text-right font-semibold text-error">{{ number_format($overdueInstallments) }}
                        </td>
                    </tr>
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4 font-bold text-on-surface">Saldo</td>
                        <td class="px-6 py-4 text-on-surface-variant">Sisa saldo pinjaman aktif</td>
                        <td class="px-6 py-4 text-right font-semibold">Rp
                            {{ number_format($activeLoansBalance, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
