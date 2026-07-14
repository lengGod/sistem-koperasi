<x-app-layout>
    <x-slot name="header">
        <h2>Laporan Koperasi</h2>
    </x-slot>

    <section class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-on-surface">Laporan Koperasi</h1>
            <p class="mt-1 text-sm text-outline">Rekapitulasi seluruh kegiatan koperasi.</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('reports.koperasi.index') }}" method="GET" class="flex gap-2">
                <input type="month" name="start_month" value="{{ $start_month }}" class="rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary text-sm">
                <input type="month" name="end_month" value="{{ $end_month }}" class="rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary text-sm">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition">
                    Filter
                </button>
            </form>
            <button type="button" 
                    onclick="window.location.href='{{ route('reports.koperasi.export', ['start_month' => $start_month, 'end_month' => $end_month], false) }}'"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined text-[20px]">download</span>
                Export ke Excel
            </button>
        </div>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest p-6">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
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
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($totalSavings, 0, ',', '.') }}</td>
                </tr>
                <tr class="transition hover:bg-surface-container">
                    <td class="px-6 py-4 font-bold text-on-surface">Pinjaman</td>
                    <td class="px-6 py-4 text-on-surface-variant">Total pokok pinjaman diberikan</td>
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($totalLoans, 0, ',', '.') }}</td>
                </tr>
                <tr class="transition hover:bg-surface-container">
                    <td class="px-6 py-4 font-bold text-on-surface">Angsuran</td>
                    <td class="px-6 py-4 text-on-surface-variant">Total angsuran lunas diterima</td>
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($totalInstallmentsPaid, 0, ',', '.') }}</td>
                </tr>
                <tr class="transition hover:bg-surface-container">
                    <td class="px-6 py-4 font-bold text-on-surface">Status</td>
                    <td class="px-6 py-4 text-on-surface-variant">Jumlah angsuran terlambat</td>
                    <td class="px-6 py-4 text-right font-semibold text-error">{{ number_format($overdueInstallments) }}</td>
                </tr>
                <tr class="transition hover:bg-surface-container">
                    <td class="px-6 py-4 font-bold text-on-surface">Saldo</td>
                    <td class="px-6 py-4 text-on-surface-variant">Sisa saldo pinjaman aktif</td>
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($activeLoansBalance, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-app-layout>
