<x-app-layout>
    <x-slot name="header">
        <h2>Edit Anggota</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Anggota</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Edit {{ $member->name }}</h1>
            <p class="mt-1 text-sm text-outline">Perbarui data anggota dengan informasi terbaru.</p>
        </div>

        <form action="{{ route('members.update', $member) }}" method="POST" class="dashboard-card rounded-3xl bg-surface-container-lowest p-5">
            @method('PUT')
            @include('members._form')
        </form>
    </section>
</x-app-layout>
