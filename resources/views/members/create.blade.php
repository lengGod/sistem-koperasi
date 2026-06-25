<x-app-layout>
    <x-slot name="header">
        <h2>Tambah Anggota</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Anggota</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Tambah Anggota Baru</h1>
            <p class="mt-1 text-sm text-outline">Lengkapi identitas anggota sebelum transaksi simpanan atau pinjaman dicatat.</p>
        </div>

        <form action="{{ route('members.store') }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @include('members._form')
        </form>
    </section>
</x-app-layout>
