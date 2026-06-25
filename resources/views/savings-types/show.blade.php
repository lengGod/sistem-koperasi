<x-app-layout>
    <x-slot name="header"><h2>Detail Jenis Simpanan</h2></x-slot>
    <section class="w-full">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Inti Finansial</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">{{ $savingsType->name }}</h1>
                <p class="mt-1 text-sm text-outline">{{ $savingsType->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('savings-types.edit', $savingsType) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">Edit</a>
                <a href="{{ route('savings-types.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">Kembali</a>
            </div>
        </div>
        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Kode</p><p class="mt-1 font-semibold">{{ $savingsType->code }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Wajib</p><p class="mt-1 font-semibold">{{ $savingsType->is_mandatory ? 'Ya' : 'Tidak' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Status</p><p class="mt-1 font-semibold">{{ $savingsType->is_active ? 'Aktif' : 'Nonaktif' }}</p></div>
                <div class="md:col-span-2"><p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Deskripsi</p><p class="mt-1 font-semibold leading-6">{{ $savingsType->description ?: '-' }}</p></div>
            </div>
        </div>
    </section>
</x-app-layout>
