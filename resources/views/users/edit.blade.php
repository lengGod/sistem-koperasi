<x-app-layout>
    <x-slot name="header">
        <h2>Edit Pengguna</h2>
    </x-slot>

    <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users._form')
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded-xl text-sm font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
