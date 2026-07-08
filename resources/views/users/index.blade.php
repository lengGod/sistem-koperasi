<x-app-layout>
    <x-slot name="header">
        <h2>Users</h2>
    </x-slot>

    <section class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-on-surface">Manajemen Pengguna</h1>
            <p class="mt-1 text-sm text-outline">Kelola data pengguna aplikasi.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Pengguna
        </a>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest p-6">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                <tr>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse ($users as $user)
                    <tr class="transition hover:bg-surface-container">
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">{{ $user->getRoleNames()->implode(', ') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('users.edit', $user) }}"
                                    class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                    Edit
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-xl border border-error-container bg-error-container px-3 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">
                            Belum ada pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
