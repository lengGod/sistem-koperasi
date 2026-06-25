<x-app-layout>
    <x-slot name="header"><h2>Tambah Simpanan</h2></x-slot>
    <section class="w-full">
        <div class="mb-6"><p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Inti Finansial</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Tambah Transaksi Simpanan</h1></div>
        <form action="{{ route('savings.store') }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @include('savings._form')
        </form>
    </section>
</x-app-layout>
