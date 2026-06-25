<x-app-layout>
    <x-slot name="header"><h2>Detail Simpanan</h2></x-slot>
    <section class="w-full">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Inti Finansial</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">{{ $saving->reference_number ?: 'Detail Simpanan' }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('savings.edit', $saving) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">Edit</a>
                <a href="{{ route('savings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Kembali</a>
            </div>
        </div>
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Anggota</p><p class="mt-1 font-semibold">{{ $saving->member?->name ?? '-' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jenis</p><p class="mt-1 font-semibold">{{ $saving->savingsType?->name ?? '-' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tipe</p><p class="mt-1 font-semibold">{{ $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Nominal</p><p class="mt-1 font-semibold">Rp {{ number_format((float) $saving->amount, 0, ',', '.') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal</p><p class="mt-1 font-semibold">{{ optional($saving->transaction_date)->format('d M Y') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Referensi</p><p class="mt-1 font-semibold">{{ $saving->reference_number ?: '-' }}</p></div>
                <div class="md:col-span-2"><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Catatan</p><p class="mt-1 font-semibold leading-6">{{ $saving->notes ?: '-' }}</p></div>
            </div>
        </div>
    </section>
</x-app-layout>
